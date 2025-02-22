<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Call extends SAM_Controller {

    public function __construct()
	{
		parent::__construct();
		// $this->load->helper('form');
		$this->load->library('session');

        if(!$this->isLogin){
            redirect('login/out');
        }        
	}

	public function index()
	{
        $path = CALL_LIST;
        $search = [];
        if(!empty($this->input->post())){
            $search = [
                'produk' => $this->input->post('produksumberdana'),
                'nama_prospek' => $this->input->post('namaprospek'),
                'kategori_nasabah' => $this->input->post('kategorinasabah'),
                'jenis_nasabah' => $this->input->post('jenisnasabah')
            ];
            $this->data['search'] = $search;
        }

        $products = $this->client_url->postCURL(PRODUCT_LIST,'',$this->data['userdata']['token']); 
        $products = json_decode($products);
        if($products!=null && !isset($products->status)){
            // Decrypt the response
            $products = json_decode($this->recure($products));
        }
        if(isset($products->status) && $products->status)
        {
            $this->data['product_list'] = $products->data;
        }

        if(!$this->data['userdata']['acl_approve']){
            $search['created_by'] = $this->data['userdata']['id_user'];
        }
        if(!$this->data['userdata']['is_sa'] && $this->data['userdata']['acl_approve']){
            $search['status'] = '1';
        }
        $search['branch_code'] = $this->data['userdata']['branch_id'];

        if(!empty($search)){
            $path = CALL_SEARCH;
            $search = $this->secure($search);
        }
        $calls = $this->client_url->postCURL($path,$search,$this->data['userdata']['token']); 
        $calls = json_decode($calls);
        if($calls!=null && !isset($calls->status)){
            // Decrypt the response
            $calls = json_decode($this->recure($calls));
        }

        if(isset($calls->status) && $calls->status)
        {
            $this->data['call_list'] = $calls->data;
        }

        $this->data['content'] = 'call/list-call';

        $this->data['javascriptLoad'] = array(
            0 => 'assets/vendors/datatables.net/js/jquery.dataTables.min.js',
            1 => 'assets/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js',
            2 => 'assets/vendors/datatables.net-buttons/js/dataTables.buttons.min.js',
            3 => 'assets/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js',
            4 => 'assets/vendors/datatables.net-buttons/js/buttons.flash.min.js',
            5 => 'assets/vendors/datatables.net-buttons/js/buttons.html5.min.js',
            6 => 'assets/vendors/datatables.net-buttons/js/buttons.print.min.js',
            7 => 'assets/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js',
            8 => 'assets/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js',
            9 => 'assets/vendors/datatables.net-responsive/js/dataTables.responsive.min.js',
            10 => 'assets/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js',
            11 => 'assets/vendors/datatables.net-scroller/js/dataTables.scroller.min.js',
            12 => 'assets/build/js/call.js'
        );

        $this->load->view('template', $this->data);
    }

    public function add()
    { 
        $provinces = $this->client_url->postCURL(PROVINCE_LIST,'',$this->data['userdata']['token']); 
        $provinces = json_decode($provinces);
        if($provinces!=null && !isset($provinces->status)){
            // Decrypt the response
            $provinces = json_decode($this->recure($provinces));
        }
        if(isset($provinces->status) && $provinces->status)
        {
            $this->data['province_list'] = $provinces->data;
        }

        $products = $this->client_url->postCURL(PRODUCT_LIST,'',$this->data['userdata']['token']); 
        $products = json_decode($products);
        if($products!=null && !isset($products->status)){
            // Decrypt the response
            $products = json_decode($this->recure($products));
        }
        if(isset($products->status) && $products->status)
        {
            $this->data['product_list'] = $products->data;
        }

        $this->data['content'] = 'call/input-call';

        $this->data['javascriptLoad'] = array(
            1 => 'assets/build/js/call.js',
            2 => 'assets/build/js/jquery.validate.min.js',
            3 => 'assets/vendors/moment/min/moment.min.js',
            4 => 'assets/vendors/bootstrap-datepicker/js/bootstrap-datepicker.js',
            5 => 'assets/vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js'
        );
        $this->load->view('template', $this->data);
    }

    public function detail($id)
    {

        $this->data['id'] = $id;
        $call = $this->client_url->postCURL(CALL_GET,$this->secure(array('id'=>$id)),$this->data['userdata']['token']);
        $call = json_decode($call);
        if($call!=null && !isset($call->status)){
            // Decrypt the response
            $call = json_decode($this->recure($call));
        }

        $this->data['data'] = $call->data;
        
        $tx_call = $this->client_url->postCURL(CALL_TRX_LIST,$this->secure(array('lead_id'=>$call->data->lead_id)),$this->data['userdata']['token']);
        $tx_call = json_decode($tx_call);
        if($tx_call!=null && !isset($tx_call->status)){
            // Decrypt the response
            $tx_call = json_decode($this->recure($tx_call));
        }

        $this->data['trx_data'] = $tx_call->data;

        $provinces = $this->client_url->postCURL(PROVINCE_LIST,'',$this->data['userdata']['token']); 
        $provinces = json_decode($provinces);
        if($provinces!=null && !isset($provinces->status)){
            // Decrypt the response
            $provinces = json_decode($this->recure($provinces));
        }
        if(isset($provinces->status) && $provinces->status)
        {
            $this->data['province_list'] = $provinces->data;
        }

        $products = $this->client_url->postCURL(PRODUCT_LIST,'',$this->data['userdata']['token']); 
        $products = json_decode($products);
        if($products!=null && !isset($products->status)){
            // Decrypt the response
            $products = json_decode($this->recure($products));
        }
        if(isset($products->status) && $products->status)
        {
            $this->data['product_list'] = $products->data;
        }

        $arr = [
            'province' => $call->data->provinsi_id
        ];

        $regency = $this->client_url->postCURL(REGENCY_LIST,$this->secure($arr),$this->data['userdata']['token']); 
        $regency = json_decode($regency);
        if($regency!=null && !isset($regency->status)){
            // Decrypt the response
            $regency = json_decode($this->recure($regency));
        }
        if(isset($regency->status) && $regency->status)
        {
            $this->data['regency_list'] = $regency->data;
        }

        $arr2 = [
            'regency' => $call->data->kota_kabupaten_id
        ];

        $district = $this->client_url->postCURL(DISTRICT_LIST,$this->secure($arr2),$this->data['userdata']['token']); 
        $district = json_decode($district);
        if($district!=null && !isset($district->status)){
            // Decrypt the response
            $district = json_decode($this->recure($district));
        }
        if(isset($district->status) && $district->status)
        {
            $this->data['district_list'] = $district->data;
        }

        $arr3 = [
            'district' => $call->data->kecamatan_id
        ];

        $village = $this->client_url->postCURL(VILLAGE_LIST,$this->secure($arr3),$this->data['userdata']['token']); 
        $village = json_decode($village);
        if($village!=null && !isset($village->status)){
            // Decrypt the response
            $village = json_decode($this->recure($village));
        }
        if(isset($village->status) && $village->status)
        {
            $this->data['village_list'] = $village->data;
        }

        $products = $this->client_url->postCURL(PRODUCT_LIST,'',$this->data['userdata']['token']); 
        $products = json_decode($products);
        if($products!=null && !isset($products->status)){
            // Decrypt the response
            $products = json_decode($this->recure($products));
        }
        if(isset($products->status) && $products->status)
        {
            $this->data['product_list'] = $products->data;
        }

        switch($call->data->approval){
            case '1' : 
                $call->data->approval_status = '<i class="fa fa-check text-success"></i> Approved'; 
                $call->data->approval_color = 'bg-info';
            break;
            case '2' : 
                $call->data->approval_status = '<i class="fa fa-times text-danger"></i> Rejected'; 
                $call->data->approval_color = 'bg-warning';
            break;
            default : 
                $call->data->approval_status = '<i class="fa fa-clock text-danger"></i> Waiting for Approval'; 
                $call->data->approval_color = 'bg-secondary';
            break;
        }

        $this->data['content'] = 'call/view-call';
        // $data['javascriptLoad'] = array(
        //     1 => 'assets/build/js/lead.js',
        //     2 => 'assets/build/js/jquery.validate.min.js'
        // );

        $this->load->view('template', $this->data);
    }

    public function edit($id)
    {
        if(!$this->data['userdata']['is_sa'] && !$this->data['userdata']['acl_edit']) {
            $this->session->set_flashdata('error_message', 'Access denied! You have no rights to access this page.');
            redirect('call');
        }

        $this->data['id'] = $id;
        $call = $this->client_url->postCURL(CALL_GET,$this->secure(array('id'=>$id)),$this->data['userdata']['token']);
        $call = json_decode($call);
        if($call!=null && !isset($call->status)){
            // Decrypt the response
            $call = json_decode($this->recure($call));
        }

        $this->data['data'] = $call->data;
        
        $tx_call = $this->client_url->postCURL(CALL_TRX_LIST,$this->secure(array('lead_id'=>$call->data->lead_id)),$this->data['userdata']['token']);
        $tx_call = json_decode($tx_call);
        if($tx_call!=null && !isset($tx_call->status)){
            // Decrypt the response
            $tx_call = json_decode($this->recure($tx_call));
        }

        $this->data['trx_data'] = $tx_call->data;

        $provinces = $this->client_url->postCURL(PROVINCE_LIST,'',$this->data['userdata']['token']); 
        $provinces = json_decode($provinces);
        if($provinces!=null && !isset($provinces->status)){
            // Decrypt the response
            $provinces = json_decode($this->recure($provinces));
        }
        if(isset($provinces->status) && $provinces->status)
        {
            $this->data['province_list'] = $provinces->data;
        }

        $products = $this->client_url->postCURL(PRODUCT_LIST,'',$this->data['userdata']['token']); 
        $products = json_decode($products);
        if($products!=null && !isset($products->status)){
            // Decrypt the response
            $products = json_decode($this->recure($products));
        }
        if(isset($products->status) && $products->status)
        {
            $this->data['product_list'] = $products->data;
        }

        $arr = [
            'province' => $call->data->provinsi_id
        ];

        $regency = $this->client_url->postCURL(REGENCY_LIST,$this->secure($arr),$this->data['userdata']['token']); 
        $regency = json_decode($regency);
        if($regency!=null && !isset($regency->status)){
            // Decrypt the response
            $regency = json_decode($this->recure($regency));
        }
        if(isset($regency->status) && $regency->status)
        {
            $this->data['regency_list'] = $regency->data;
        }

        $arr2 = [
            'regency' => $call->data->kota_kabupaten_id
        ];

        $district = $this->client_url->postCURL(DISTRICT_LIST,$this->secure($arr2),$this->data['userdata']['token']); 
        $district = json_decode($district);
        if($district!=null && !isset($district->status)){
            // Decrypt the response
            $district = json_decode($this->recure($district));
        }
        if(isset($district->status) && $district->status)
        {
            $this->data['district_list'] = $district->data;
        }

        $arr3 = [
            'district' => $call->data->kecamatan_id
        ];

        $village = $this->client_url->postCURL(VILLAGE_LIST,$this->secure($arr3),$this->data['userdata']['token']); 
        $village = json_decode($village);
        if($village!=null && !isset($village->status)){
            // Decrypt the response
            $village = json_decode($this->recure($village));
        }
        if(isset($village->status) && $village->status)
        {
            $this->data['village_list'] = $village->data;
        }

        $products = $this->client_url->postCURL(PRODUCT_LIST,'',$this->data['userdata']['token']); 
        $products = json_decode($products);
        if($products!=null && !isset($products->status)){
            // Decrypt the response
            $products = json_decode($this->recure($products));
        }
        if(isset($products->status) && $products->status)
        {
            $this->data['product_list'] = $products->data;
        }

        $this->data['content'] = 'call/edit-call';
        $this->data['javascriptLoad'] = array(
            1 => 'assets/build/js/call.js',
            2 => 'assets/build/js/jquery.validate.min.js',
            3 => 'assets/vendors/moment/min/moment.min.js',
            4 => 'assets/vendors/bootstrap-datepicker/js/bootstrap-datepicker.js',
            5 => 'assets/vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js'
        );

        $this->load->view('template', $this->data);
    }

    public function saveData()
    {
        extract($this->input->post());

        $status = "0";
        if(isset($submit)){
            $status = "1";
        }

        $arr = [];
    
        $path = CALL_CREATE;
        $return_path = 'call/add';
        if(isset($id)){
            $path = CALL_UPDATE;
            $return_path = 'call/edit/'.$id;
            $arr['id'] = $id;
            $arr['tx_id'] = $tx_id;
        }
        
        $datecall = date_format(date_create(str_replace("/", "-", $datecall)),"Y-m-d");
        $timecall = date('H:i:s',strtotime($timecall));

        $arr['lead_id'] = $lead_id;
        $arr['attempt'] = $call_ke;
        $arr['issued_date'] = $datecall;
        $arr['issued_time'] = $timecall;
        $arr['additional_info'] = $additionalinfo;
        $arr['user'] = $this->data['userdata']['id_user'];
        $arr['status'] = $status;

        if(isset($_FILES['attachment']) && $_FILES['attachment']['error']==0){
            $config['upload_path']   = './uploads/';
            $config['allowed_types'] = 'gif|jpg|png|pdf|doc|docx|xls|xlsx';
            $config['max_size']      = 8192;
    
            $filename = time()."_".$_FILES["attachment"]['name'];
            $config['file_name']     = $filename;
    
            $this->load->library('upload',$config);
    
            if ( ! $this->upload->do_upload('attachment'))
            {
                    $error = array('error' => $this->upload->display_errors());
    
                    echo 'Error on Upload : '.json_encode($error);
            }  
            else
            {    
                $uploads = $this->upload->data();
    
                $arr['attachment'] = $uploads['file_name'];
                $arr['file_path'] = $uploads['file_path'];
                $arr['file_size'] = $uploads['file_size'];
                $arr['file_type'] = $uploads['file_type'];
            }
        }

        $saving = $this->client_url->postCURL($path,$this->secure($arr),$this->data['userdata']['token']); 
        $saving = json_decode($saving);
        if($saving!=null && !isset($saving->status)){
            // Decrypt the response
            $saving = json_decode($this->recure($saving));
        }
        if(isset($saving->status) && !$saving->status)
        {
            $this->session->set_flashdata('error_message', $saving->message);
            redirect($return_path);
        }
        redirect('call');
    }

    public function getNamaProspek()
    {
        $response = null;
        extract($this->input->post());

        $arr = [
            'kategori_nasabah' => $kategori_nasabah,
            'user' => $this->data['userdata']['id_user']
        ];

        $prospects = $this->client_url->postCURL(LEAD_PROSPECT_LIST,$this->secure($arr),$this->data['userdata']['token']); 
        $prospects = json_decode($prospects);
        if($prospects!=null && !isset($prospects->status)){
            // Decrypt the response
            $prospects = json_decode($this->recure($prospects));
        }
        if(isset($prospects->status) && $prospects->status)
        {
            $response = $prospects->data;
        }

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function getDetailProspek()
    {
        $response = null;
        extract($this->input->post());

        $this->data['id'] = $id;
        $lead = $this->client_url->postCURL(LEAD_GET,$this->secure(array('id'=>$id)),$this->data['userdata']['token']);
        $lead = json_decode($lead);
        if($lead!=null && !isset($lead->status)){
            // Decrypt the response
            $lead = json_decode($this->recure($lead));
        }
        if(isset($lead->status) && $lead->status)
        {
            $response = $lead->data;
        }

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    function getAttempt()
    {
        $response = null;
        extract($this->input->post());
        
        $calls = $this->client_url->postCURL(CALL_ATTEMPT,$this->secure(array('id'=>$id)),$this->data['userdata']['token']);
        $calls = json_decode($calls);
        if($calls!=null && !isset($calls->status)){
            // Decrypt the response
            $calls = json_decode($this->recure($calls));
        }
        if(isset($calls->status) && $calls->status)
        {
            $response = $calls->data;
        }

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    function approval()
    {
        if(!$this->data['userdata']['is_sa'] && !$this->data['userdata']['acl_approve']) {
            $this->session->set_flashdata('error_message', 'Access denied! You have no rights to access this page.');
            redirect('lead');
        }
        extract($this->input->post());

        $approval = '1';
        if(isset($reject)){
            $approval = '2';
        }

        $arr = [
            'id' => $id,
            'approval' => $approval,
            'approval_by' => $this->data['userdata']['id_user']
        ]; 

        $saving = $this->client_url->postCURL(CALL_APPROVE,$this->secure($arr),$this->data['userdata']['token']); 
        $saving = json_decode($saving);
        if($saving!=null && !isset($saving->status)){
            // Decrypt the response
            $saving = json_decode($this->recure($saving));
        }
        if(isset($saving->status) && !$saving->status)
        {
            $this->session->set_flashdata('error_message', $saving->message);
        }
        redirect('call');
    }

    function remove($id)
    {
        $arr = [
            'id' => $id
        ]; 

        $delete = $this->client_url->postCURL(CALL_REMOVE,$this->secure($arr),$this->data['userdata']['token']); 
        $delete = json_decode($delete);
        if($delete!=null && !isset($delete->status)){
            // Decrypt the response
            $delete = json_decode($this->recure($delete));
        }
        if(isset($delete->status) && !$delete->status)
        {
            $this->session->set_flashdata('error_message', $delete->message);
        }
        redirect('call');
    }
}
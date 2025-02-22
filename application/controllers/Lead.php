<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lead extends SAM_Controller {

    public function __construct()
	{
		parent::__construct();
		$this->load->library('session');     

        if(!$this->isLogin){
            redirect('login/out');
        }     
	}

	public function index()
	{
        $path = LEAD_LIST;
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
            $path = LEAD_SEARCH;
            $search = $this->secure($search);
        }
        $leads = $this->client_url->postCURL($path,$search,$this->data['userdata']['token']); 
        $leads = json_decode($leads);
        if($leads!=null && !isset($leads->status)){
            // Decrypt the response
            $leads = json_decode($this->recure($leads));
        }

        if(isset($leads->status) && $leads->status)
        {
            $this->data['lead_list'] = $leads->data;
        }
        
        $this->data['content'] = 'lead/list-lead';

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
            12 => 'assets/build/js/lead.js'
        );

        $this->load->view('template', $this->data);
    }

	public function search()
	{
        // $path = LEAD_LIST;
        $path = LEAD_SEARCH;
        $search = '';
        if(!empty($this->input->post())){
            $forms = [
                'produk' => $this->input->post('produksumberdana'),
                'nama_prospek' => $this->input->post('namaprospek'),
                'kategori_nasabah' => $this->input->post('kategorinasabah'),
                'jenis_nasabah' => $this->input->post('jenisnasabah')
            ];
            $search = $this->secure($forms);
            // $this->data['search'] = $forms;
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
        $this->data['search']['branch_code'] = $this->data['userdata']['branch_id'];

        $leads = $this->client_url->postCURL($path,$search,$this->data['userdata']['token']); 
        $leads = json_decode($leads);
        if($leads!=null && !isset($leads->status)){
            // Decrypt the response
            $leads = json_decode($this->recure($leads));
        }

        if(isset($leads->status) && $leads->status)
        {
            $this->data['lead_list'] = $leads->data;
        }
        $this->data['content'] = 'lead/list-lead';

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
            12 => 'assets/build/js/lead.js'
        );

        $this->load->view('template', $this->data);
    }

    public function add()
    {
        if(!$this->data['userdata']['is_sa'] && !$this->data['userdata']['acl_input']) {
            $this->session->set_flashdata('error_message', 'Access denied! You have no rights to access this page.');
            redirect('lead');
        }

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

        $this->data['content'] = 'lead/input-lead';
        $this->data['javascriptLoad'] = array(
            1 => 'assets/build/js/lead.js',
            2 => 'assets/build/js/jquery.validate.min.js'
        );

        $this->load->view('template', $this->data);
    }

    public function detail($id)
    {
        $this->data['content'] = 'lead/view-lead';

        $this->data['id'] = $id;
        $lead = $this->client_url->postCURL(LEAD_GET,$this->secure(array('id'=>$id)),$this->data['userdata']['token']);
        $lead = json_decode($lead);
        if($lead!=null && !isset($lead->status)){
            // Decrypt the response
            $lead = json_decode($this->recure($lead));
        }

        switch($lead->data->approval){
            case '1' : 
                $lead->data->approval_status = '<i class="fa fa-check text-success"></i> Approved'; 
                $lead->data->approval_color = 'bg-info';
            break;
            case '2' : 
                $lead->data->approval_status = '<i class="fa fa-times text-danger"></i> Rejected'; 
                $lead->data->approval_color = 'bg-warning';
            break;
            default : 
                $lead->data->approval_status = '<i class="fa fa-clock text-danger"></i> Waiting for Approval'; 
                $lead->data->approval_color = 'bg-default';
            break;
        }

        $this->data['data'] = $lead->data;
        
        $this->load->view('template', $this->data);
    }

    public function edit($id)
    {
        if(!$this->data['userdata']['is_sa'] && !$this->data['userdata']['acl_edit']) {
            $this->session->set_flashdata('error_message', 'Access denied! You have no rights to access this page.');
            redirect('lead');
        }
        $this->data['content'] = 'lead/edit-lead';

        $this->data['id'] = $id;
        $lead = $this->client_url->postCURL(LEAD_GET,$this->secure(array('id'=>$id)),$this->data['userdata']['token']);
        $lead = json_decode($lead);
        if($lead!=null && !isset($lead->status)){
            // Decrypt the response
            $lead = json_decode($this->recure($lead));
        }

        $this->data['data'] = $lead->data;

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
            'province' => $lead->data->provinsi_id
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
            'regency' => $lead->data->kota_kabupaten_id
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
            'district' => $lead->data->kecamatan_id
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

        $this->data['javascriptLoad'] = array(
            1 => 'assets/build/js/lead.js',
            2 => 'assets/build/js/jquery.validate.min.js'
        );

        $this->load->view('template', $this->data);
    }

    function getRegency()
    {
        $response = null;
        extract($this->input->post());

        $arr = [
            'province' => $province
        ];

        $regency = $this->client_url->postCURL(REGENCY_LIST,$this->secure($arr),$this->data['userdata']['token']); 
        $regency = json_decode($regency);
        if($regency!=null && !isset($regency->status)){
            // Decrypt the response
            $regency = json_decode($this->recure($regency));
        }
        if(isset($regency->status) && $regency->status)
        {
            $response = $regency->data;
        }

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    function getDistrict()
    {
        $response = null;
        extract($this->input->post());

        $arr = [
            'regency' => $regency
        ];

        $district = $this->client_url->postCURL(DISTRICT_LIST,$this->secure($arr),$this->data['userdata']['token']); 
        $district = json_decode($district);
        if($district!=null && !isset($district->status)){
            // Decrypt the response
            $district = json_decode($this->recure($district));
        }
        if(isset($district->status) && $district->status)
        {
            $response = $district->data;
        }

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    function getVillage()
    {
        $response = null;
        extract($this->input->post());

        $arr = [
            'district' => $district
        ];

        $village = $this->client_url->postCURL(VILLAGE_LIST,$this->secure($arr),$this->data['userdata']['token']); 
        $village = json_decode($village);
        if($village!=null && !isset($village->status)){
            // Decrypt the response
            $village = json_decode($this->recure($village));
        }
        if(isset($village->status) && $village->status)
        {
            $response = $village->data;
        }

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    function saveData()
    {
        extract($this->input->post());

        $status = "0";
        if(isset($submit)){
            $status = "1";
        }

        $arr = [
            'kategori_nasabah' => $kategorinasabah,
            'nama_prospek' => $namaprospect,
            'jenis_nasabah' => $jenisnasabah,
            'alamat' => $alamat,
            'provinsi' => $provinsi,
            'kota_kabupaten' => $kota,
            'kecamatan' => $kecamatan,
            'kelurahan' => $kelurahan,
            'kontak_person' => $contactperson,
            'no_kontak' => $contactnumber,
            'potensi_dana' => $potensidana,
            'produk' => $produksumberdana,
            'additional_info' => $additionalinfo,
            'user' => $this->data['userdata']['id_user'],
            'status' => $status
        ]; 

        $path = LEAD_CREATE;
        $return_path = 'lead/add';
        if(isset($id)){
            $arr['id'] = $id;
            $path = LEAD_UPDATE;
            $return_path = 'lead/edit/'.$id;
            unset($arr['user']);

            $datelead = date('Y-m-d',strtotime(str_replace('/','-',$datelead)));
            $timelead = date('H:i:s',strtotime($timelead));

            $arr['created_date'] = $datelead." ".$timelead;
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
        redirect('lead');
    }

    function approve($id)
    {
        $arr = [
            'id' => $id,
            'approval' => 1,
            'approval_by' => $this->data['userdata']['id_user']
        ]; 

        $saving = $this->client_url->postCURL(LEAD_APPROVE,$this->secure($arr),$this->data['userdata']['token']); 
        $saving = json_decode($saving);
        if($saving!=null && !isset($saving->status)){
            // Decrypt the response
            $saving = json_decode($this->recure($saving));
        }
        if(isset($saving->status) && !$saving->status)
        {
            $this->session->set_flashdata('error_message', $saving->message);
        }
        redirect('lead');
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

        $saving = $this->client_url->postCURL(LEAD_APPROVE,$this->secure($arr),$this->data['userdata']['token']); 
        $saving = json_decode($saving);
        if($saving!=null && !isset($saving->status)){
            // Decrypt the response
            $saving = json_decode($this->recure($saving));
        }
        if(isset($saving->status) && !$saving->status)
        {
            $this->session->set_flashdata('error_message', $saving->message);
        }
        redirect('lead');
    }

    function remove($id)
    {
        $arr = [
            'id' => $id
        ]; 

        $delete = $this->client_url->postCURL(LEAD_REMOVE,$this->secure($arr),$this->data['userdata']['token']); 
        $delete = json_decode($delete);
        if($delete!=null && !isset($delete->status)){
            // Decrypt the response
            $delete = json_decode($this->recure($delete));
        }
        if(isset($delete->status) && !$delete->status)
        {
            $this->session->set_flashdata('error_message', $delete->message);
        }
        redirect('lead');
    }
}
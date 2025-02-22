<!-- page content -->
<div class="right_col" role="main">
    <!-- top tiles -->
    <div class="row" style="display: inline-block;" >
    <div class="tile_count">
        <div class="col-md-12 col-sm-12 tile_stats_count">
            <div class="count">Meet</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="x_panel" style="border-radius: 8px">
            <div class="x_title">
                <h2>Kriteria Pencarian</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <form id="search" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                    <div class="row">
                        <div class="col-md-3 col-sm-3">
                            <div class="form-group">
                                <label for="produksumberdana" style="font-size: 0.9rem">Produk Sumber Dana</label>
                                <select style="border-radius: 6px; color: #495057;" id="produksumberdana" name="produksumberdana" class="form-control" >
                                    <option value="">Pilih Produk</option>
                                    <?php if(isset($product_list) && !empty($product_list)) : 
                                        foreach ($product_list as $prd) {
                                            $selected = isset($search['produk'])&&$search['produk']==$prd->product_id? 'selected':'';
                                            echo '<option value="'.$prd->product_id.'" '.$selected.'>'.$prd->product_name.'</option>';
                                        }
                                    endif; ?>
                                </select>
                            </div>                                        
                        </div>
                        <div class="col-md-3 col-sm-3">
                            <div class="form-group">
                                <label for="namaprospek" style="font-size: 0.9rem">Nama Prospek</label>
                                <input type="text" style="border-radius: 6px; color: #495057 !important;" class="form-control" placeholder="Nama Prospek" id="namaprospek" name="namaprospek" value="<?php echo (isset($search['nama_prospek'])? $search['nama_prospek']:''); ?>">
                            </div>                                        
                        </div>
                        <div class="col-md-3 col-sm-3">
                            <div class="form-group">
                                <label for="kategorinasabah" style="font-size: 0.9rem">Kategori Nasabah</label>
                                <select style="border-radius: 6px; color: #495057;" class="form-control" id="kategorinasabah" name="kategorinasabah">
                                    <option value="">Pilih Kategori Nasabah</option>
                                    <option value="1" <?php echo (isset($search['kategori_nasabah'])&&$search['kategori_nasabah']==1? 'selected':''); ?>>New</option>
                                    <option value="2" <?php echo (isset($search['kategori_nasabah'])&&$search['kategori_nasabah']==2? 'selected':''); ?>>Exist</option>
                                </select>
                            </div>                                        
                        </div>
                        <div class="col-md-3 col-sm-3">
                            <div class="form-group">
                                <label for="jenisnasabah" style="font-size: 0.9rem">Jenis Nasabah</label>
                                <select style="border-radius: 6px; color: #495057;" id="jenisnasabah" name="jenisnasabah" class="form-control" >
                                    <option value="">Pilih Jenis Nasabah</option>
                                    <option value="1" <?php echo (isset($search['jenis_nasabah'])&&$search['jenis_nasabah']==1? 'selected':''); ?>>Perorangan</option>
                                    <option value="2" <?php echo (isset($search['jenis_nasabah'])&&$search['jenis_nasabah']==2? 'selected':''); ?>>Institusi</option>
                                </select>
                            </div>                                        
                        </div>
                        <div class="col-md-12 col-sm-12 text-center">
                            <div class="form-group">
                                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" style="border-radius: 6px" class="btn btn-info btn-sm">Show All</a>
                                <input style="border-radius: 6px" type="submit" class="btn btn-primary btn-sm" value="Search">
                                <button style="border-radius: 6px" type="button" class="btn btn-warning btn-sm" onclick="resetForm()">Reset</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12 col-sm-12">
        <div class="x_panel" style="border-radius: 8px">
            <?php if($userdata['is_sa'] || (!$userdata['is_sa'] && $userdata['acl_input']) ) : ?>
            <div class="x_title">
                <button style="border-radius: 6px" type="button" class="btn btn-primary btn-sm"><i class="fa fa-plus-circle "></i><a style="color: white;" href="<?php echo site_url('meet/add') ?>"> New Data</a></button>   
                <div class="clearfix"></div>
            </div>
            <?php endif; ?>
            <div class="x_content">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card-box table-responsive">
                            <table id="dataMeet" class="table table-striped table-bordered nowrap" width="100%">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Prospect</th>
                                        <th>Jenis Nasabah</th>
                                        <th>Potensi Dana Masuk</th>
                                        <th>Meet Date</th>
                                        <th>Input Date</th>
                                        <th>History Lead</th>	  
                                        <th>History Call</th>	  
                                        <th>FU Close</th>	
                                        <th>Status</th> 
                                        <th>Action</th>	   
                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
                                    if(isset($meet_list) && !empty($meet_list)) : 
                                        $no = 1;
                                        foreach ($meet_list as $meet) {
                                            $approval_status = 'Draft';
                                            if($meet->status > 0){
                                                switch($meet->approval){
                                                    case '1' : $approval_status = '<i class="fa fa-check text-success"></i> Approved'; break;
                                                    case '2' : $approval_status = '<i class="fa fa-times text-danger"></i> Rejected'; break;
                                                    default : $approval_status = 'Waiting for Approval'; break;
                                                }
                                            }

                                            echo '<tr>';
                                            echo '<td width="3%">'.$no.'</td>';
                                            echo '<td><a href="'.site_url('meet/detail/'.$meet->meet_id).'" title="View Detail" target="_blank">'.$meet->nama_prospek.'</a></td>';
                                            echo '<td>'.($meet->jenis_nasabah==1?'Perorangan':'Institusi').'</td>';
                                            // echo '<td>'.$meet->product_name.'</td>';
                                            echo '<td>'.number_format($meet->potensi_dana).'</td>';
                                            echo '<td>'.(!is_null($meet->meet_date)?date('d-m-Y',strtotime($meet->meet_date)):'-').'</td>';
                                            echo '<td>'.date('d-m-Y',strtotime($meet->created_date)).'</td>';
                                            echo '<td>'.(!is_null($meet->lead_date)?date('d-m-Y',strtotime($meet->lead_date)):'-').'</td>';
                                            echo '<td>'.(!is_null($meet->call_date)?date('d-m-Y',strtotime($meet->call_date)):'-').'</td>';
                                            echo '<td>'.(!is_null($meet->close_date)?date('d-m-Y',strtotime($meet->close_date)):'-').'</td>';
                                            echo '<td>'.$approval_status.'</td>';
                                            echo '<td class="text-center">';
                                            if((is_null($meet->status) && $userdata['acl_edit']) || ($meet->status==0 && $userdata['acl_edit']) || ($meet->approval=='2' && $userdata['acl_edit'])) :
                                            echo '<a class="btn btn-link btn-sm" href="'.site_url('meet/edit/'.$meet->meet_id).'" title="Edit"><i class="fa fa-pencil"></i></a>';
                                            endif;
                                            if((is_null($meet->status) && $userdata['acl_delete']) || ($meet->status==0 && $userdata['acl_delete'])) :
                                            echo '<a class="btn btn-link btn-sm" href="'.site_url('meet/remove/'.$meet->lead_id).'" title="Delete" onclick="return confirm(\'Apakah Anda Yakin?\')"><i class="fa fa-trash"></i></a>';
                                            endif;
                                            if(is_null($meet->approval) && $meet->status==1 && $userdata['acl_approve']) :
                                                if($userdata['acl_approve']){
                                                    echo '<a class="btn btn-link btn-sm" href="'.site_url('meet/detail/'.$meet->meet_id).'" title="Approval"><i class="fa fa-check"></i></a>';      
                                                }else{
                                                    echo '<i class="fa fa-lock"></i> LOCKED';
                                                }
                                            endif;
                                            echo '</td>';

                                            $no++;
                                        }
                                    endif;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <!-- /top tiles -->
    <br />
</div>
<!-- /page content-->
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Testimonial extends Home_Controller {

    public function __construct()
    {
        parent::__construct();
        //check auth
        if (!is_admin() && !is_user()) {
            redirect(base_url());
        }
    }


    public function index()
    {
        if (is_admin()) {
            $business_id = 0 ;
        }

        if (is_user()) {
            $business_id = $this->business->uid ;
        }

        $data = array();
        $data['page_title'] = 'Testimonials';  
        $data['testimonial'] = FALSE;
        $data['languages'] = $this->admin_model->select('language');
        $data['testimonials'] = $this->admin_model->get_testimonials_by_type($business_id);
        //echo '<pre>'; print_r($data['testimonials']); exit();
        $data['main_content'] = $this->load->view('admin/testimonial',$data,TRUE);
        $this->load->view('admin/index',$data);
    }



    public function add()
    {	
        check_status();
        
        if($_POST)
        {   

            if (user()->role == 'admin') {
                $business_id = 0;
            }

            if (user()->role == 'user') {
                $business_id = $this->business->uid;
            }


            $id = $this->input->post('id', true);
            $data=array(
                'user_id' => user()->id,
                'business_id' => $business_id,
                'lang_id' => $this->input->post('language'),
                'name' => $this->input->post('name'),
                'designation' => $this->input->post('designation'),
                'feedback' => $this->input->post('feedback')
            );

            //if id available info will be edited
            if ($id != '') {
                $this->admin_model->edit_option($data, $id, 'testimonials');
                $this->session->set_flashdata('msg', trans('updated-successfully')); 
            } else {
                $id = $this->admin_model->insert($data, 'testimonials');
                $this->session->set_flashdata('msg', trans('inserted-successfully')); 
            }

            // insert photos
            if($_FILES['photo']['name'] != ''){
                $up_load = $this->admin_model->upload_image('600');
                $data_img = array(
                    'image' => $up_load['images'],
                    'thumb' => $up_load['thumb']
                );
                $this->admin_model->edit_option($data_img, $id, 'testimonials');   
            }
            
            redirect(base_url('admin/testimonial'));

        }      
        
    }

    public function edit($id)
    {  
        $data = array();
        $data['page_title'] = 'Edit';
        $data['languages'] = $this->admin_model->select('language');   
        $data['testimonial'] = $this->admin_model->select_option($id, 'testimonials');
        $data['main_content'] = $this->load->view('admin/testimonial',$data,TRUE);
        $this->load->view('admin/index',$data);
    }

    
    public function active($id) 
    {
        $data = array(
            'status' => 1
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'testimonials');
        $this->session->set_flashdata('msg', trans('activate-successfully')); 
        redirect(base_url('admin/testimonial'));
    }

    public function deactive($id) 
    {
        $data = array(
            'status' => 0
        );
        $data = $this->security->xss_clean($data);
        $this->admin_model->update($data, $id,'testimonials');
        $this->session->set_flashdata('msg', trans('deactivate-successfully')); 
        redirect(base_url('admin/testimonial'));
    }

    public function delete($id)
    {
        $this->admin_model->delete($id,'testimonials'); 
        echo json_encode(array('st' => 1));
    }

}
	


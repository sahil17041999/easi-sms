<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
require APPPATH . '/libraries/BaseController.php';

class Easisms extends BaseController
{

    private $CI;
    function __construct()
    {
        parent::__construct();
        $this->load->model('sms_model');
        $this->load->library('Skylline_sms');
        $this->isLoggedIn();
        $this->CI = &get_instance();
    }

    public function index()
    {

        $sms_response = $this->sms_model->get_sms_log_response();
        if (valid_array($sms_response) == true) {
            $page_data['sms_log_response'] =  $sms_response;
        } else {
            $page_data['sms_log_response'] = '';
        }
        $this->loadViews("Easi_SMS/sms", $page_data);
    }

    public function contact()
    {


        $cols = ' * ';
        $tmp_data = $this->custom_db->single_table_records('contacts', $cols);
        if (valid_array($tmp_data['data']) == true) {
            $page_data['sms_contacts_data'] = $tmp_data['data'];
        } else {
            $page_data['sms_contacts_data'] = '';
        }

        $this->loadViews("Easi_SMS/sms_contact", $page_data);
    }


    public function add_contact()
    {
        if ($this->input->is_ajax_request()) {
            if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] != 0) {
                echo json_encode(['status' => 'error', 'message' => 'File upload failed.']);
                return;
            }

            $contactLimit = $this->input->post('contact_limit');
            if (empty($contactLimit) || !is_numeric($contactLimit) || $contactLimit <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Please specify a valid number of contacts to import.']);
                return;
            }

            $file = $_FILES['import_file']['tmp_name'];
            $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);
            $validExtensions = ['xls', 'xlsx', 'csv'];
            if (!in_array(strtolower($extension), $validExtensions)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid file type.']);
                return;
            }

            $rows = [];
            if ($extension === 'csv') {
                if (($handle = fopen($file, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $rows[] = $data;
                    }
                    fclose($handle);
                }
            }
            $contacts = [];
            $invalidRows = [];
            foreach ($rows as $index => $row) {
                if ($index === 0) {
                    continue;
                }
                if (count($row) < 1 || !preg_match('/^\d+$/', trim($row[0]))) {
                    $invalidRows[] = $index + 1;
                    continue;
                }
                $contacts[] = [
                    'contacts' => trim($row[0])
                ];
                if (count($contacts) >= $contactLimit) {
                    break;
                }
            }
            if (empty($contacts)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'All rows are invalid or no contacts within the specified limit. No contacts imported.'
                ]);
                return;
            }
            $channelName = $this->input->post('channel_name');
            if (empty($channelName)) {
                echo json_encode(['status' => 'error', 'message' => 'Channel name is required.']);
                return;
            }
            $data['channel_name'] = $channelName;
            $data['contacts'] = json_encode($contacts);
            $this->custom_db->insert_record('contacts', $data);
            $responseMessage = 'Contacts imported successfully.';
            if (!empty($invalidRows)) {
                $responseMessage .= ' Skipped invalid rows: ' . implode(', ', $invalidRows) . '.';
            }
            echo json_encode(['status' => 'success', 'message' => $responseMessage]);
        } else {
            $this->loadViews("Easi_SMS/add_contact");
        }
    }




    public function update_contact($id)
    {
        if ($this->input->is_ajax_request()) {
            if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] != 0) {
                echo json_encode(['status' => 'error', 'message' => 'File upload failed.']);
                return;
            }
            $file = $_FILES['import_file']['tmp_name'];
            $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);
            $validExtensions = ['xls', 'xlsx', 'csv'];
            if (!in_array(strtolower($extension), $validExtensions)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid file type.']);
                return;
            }
            $rows = [];
            if ($extension === 'csv') {
                if (($handle = fopen($file, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $rows[] = $data;
                    }
                    fclose($handle);
                }
            }
            $contactLimit = $this->input->post('contact_limit');
            if (!is_numeric($contactLimit) || $contactLimit <= 0) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid contact limit specified.']);
                return;
            }
            $newContacts = [];
            $invalidRows = [];
            $duplicates = [];
            $existingData = $this->sms_model->get_contacts($id);
            $existingContacts = [];
            if (!empty($existingData[0]['contacts'])) {
                $existingContacts = json_decode($existingData[0]['contacts'], true);
            }
            foreach ($rows as $index => $row) {
                if ($index === 0) {
                    continue;
                }
                if (count($row) < 1 || !preg_match('/^\d+$/', trim($row[0]))) {
                    $invalidRows[] = $index + 1;
                    continue;
                }
                $contact = trim($row[0]);
                if (in_array(['contacts' => $contact], $existingContacts) || in_array(['contacts' => $contact], $newContacts)) {
                    $duplicates[] = $contact;
                    continue;
                }
                $newContacts[] = [
                    'contacts' => $contact
                ];
                if (count($newContacts) >= $contactLimit) {
                    break;
                }
            }
            if (empty($newContacts)) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No new contacts to import. Skipped rows'
                ]);
                return;
            }
            $mergedContacts = array_merge($existingContacts, $newContacts);
            $data['contacts'] = json_encode($mergedContacts);
            $this->custom_db->update_record('contacts', $data, ['id' => $id]);

            $responseMessage = 'Contacts imported and updated successfully.';
            if (!empty($invalidRows)) {
                $responseMessage .= ' Skipped invalid rows';
            }
            if (!empty($duplicates)) {
                $responseMessage .= ' Skipped duplicate contacts';
            }
            echo json_encode(['status' => 'success', 'message' => $responseMessage]);
        } else {
            $page_data['contact_old_data'] = $this->sms_model->get_contacts($id);
            if (valid_array($page_data['contact_old_data'][0]) == true) {
                $page_data['id'] = $page_data['contact_old_data'][0]['id'];
                $page_data['channel_name'] = $page_data['contact_old_data'][0]['channel_name'];
                $page_data['contacts'] = $page_data['contact_old_data'][0]['contacts'];
            } else {
                $page_data['contact_old_data'] = '';
            }
            $this->loadViews("Easi_SMS/update_contact", $page_data);
        }
    }

    public function sms_contacts_status($id)
    {
        $status = $this->input->post('status');
        $this->sms_model->get_sms_contact_status($id, $status);
    }

    public function delete__contacts($id)
    {
        $_sms_contacts = $this->custom_db->delete_record('contacts', array('id' => $id));
        if ($_sms_contacts > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Contacts deleted successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete Contacts']);
        }
    }

    public function delete_selected_contacts()
    {
        $ids = $this->input->post('ids');
        if (empty($ids)) {
            echo json_encode(['status' => 'error', 'message' => 'No contacts selected.']);
            return;
        }
        $this->db->where_in('id', $ids);
        if ($this->db->delete('contacts')) {
            echo json_encode(['status' => 'success', 'message' => 'Selected contacts deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete selected contacts.']);
        }
    }


    public function sms_template()
    {
        $cols = ' * ';
        $sms_template = $this->custom_db->single_table_records('sms_template', $cols);
        if (valid_array($sms_template['data']) == true) {
            $page_data['sms_template_data'] = $sms_template['data'];
        } else {
            $page_data['sms_template_data'] = '';
        }
        $this->loadViews("Easi_SMS/sms_template", $page_data);
    }

    public function add_sms_template()
    {
        if ($this->input->is_ajax_request()) {
            $page_data['form_data'] =  $this->input->post();
            $data['template_name'] = $page_data['form_data']['template_name'];
            $data['template_content'] = $page_data['form_data']['template_content'];
            $data['status'] = $page_data['form_data']['status'];
            $sms_template_RECORD = $this->custom_db->insert_record('sms_template', $data);
            if ($sms_template_RECORD) {
                $response = [
                    'success' => true,
                    'message' => 'SMS template added successfully!'
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Failed to add SMS template. Please try again.'
                ];
            }
            echo json_encode($response);
            return;
        }
        $this->loadViews("Easi_SMS/add_sms_template");
    }

    public function update_sms_template($template_id)
    {
        if ($this->input->is_ajax_request()) {
            $page_data['form_data'] =  $this->input->post();
            $data['template_name'] = $page_data['form_data']['template_name'];
            $data['template_content'] = $page_data['form_data']['template_content'];
            $_OLD_template_RECORD = $this->custom_db->update_record('sms_template', $data, ['template_id ' => $template_id]);
            if ($_OLD_template_RECORD) {
                $response = [
                    'success' => true,
                    'message' => 'SMS template Updated successfully!'
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Failed to add SMS template. Please try again.'
                ];
            }
            echo json_encode($response);
            return;
        }
        if ($template_id) {
            $page_data['get_sms_template_data'] = $this->sms_model->get_sms_template_data($template_id);
            $page_data['template_id'] = $page_data['get_sms_template_data'][0]['template_id'];
            $page_data['template_name'] = $page_data['get_sms_template_data'][0]['template_name'];
            $page_data['template_content'] = $page_data['get_sms_template_data'][0]['template_content'];
        } else {
            $page_data['get_sms_template_data']  = [];
        }
        $this->loadViews("Easi_SMS/update_sms_template", $page_data);
    }


    public function sms_template_status($template_id)
    {
        $status = $this->input->post('status');
        $this->sms_model->get_sms_template_status($template_id, $status);
    }


    public function delete__template($template_id)
    {
        $_sms_template = $this->custom_db->delete_record('sms_template', array('template_id ' => $template_id));
        if ($_sms_template > 0) {
            echo json_encode(['status' => 'success', 'message' => 'sms template deleted successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete sms template']);
        }
    }

    public function delete_selected_sms_template()
    {
        $template_id  = $this->input->post('template_id');
        if (empty($template_id)) {
            echo json_encode(['status' => 'error', 'message' => 'No sms template selected.']);
            return;
        }
        $this->db->where_in('template_id', $template_id);
        if ($this->db->delete('sms_template')) {
            echo json_encode(['status' => 'success', 'message' => 'Selected sms template deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete selected sms template.']);
        }
    }


    public function sms()
    {
        $cols = ' * ';
        $tmp_data = $this->custom_db->single_table_records('contacts', $cols);
        if (valid_array($tmp_data['data']) == true) {
            $page_data['sms_contacts_list'] = $tmp_data['data'];
        } else {
            $page_data['sms_contacts_list'] = '';
        }

        $cols = ' * ';
        $sms_template = $this->custom_db->single_table_records(' sms_template', $cols);
        if (valid_array($sms_template['data']) == true) {
            $page_data['sms_template_list'] = $sms_template['data'];
        } else {
            $page_data['sms_template_list'] = '';
        }
        $this->loadViews("Easi_SMS/send_sms", $page_data);
    }


    public function send_sms()
    {

        $page_data['form_data'] = $this->input->post();
        $channelName =  $page_data['form_data']['channel_name'];
        $no_of_mcc =  $page_data['form_data']['no_of_mcc'];
        if ($page_data['form_data']['smsType'] == 'single') {
            $numbers = $page_data['form_data']['to'];
            $to = $numbers[0];
        } else {
            $numbers = $page_data['form_data']['to'];
            $to =  explode(',', $numbers[0]);
        }
        $message = $page_data['form_data']['message'];
        $message = str_replace("’", "'", $message);
        if (empty($to) || empty($message)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
            return;
        }
        if (!is_array($to)) {
            $to = [$to];
        }
        $recipient_no = array_map(function ($number) {
            return '+' . ltrim($number, '+');
        }, $to);
        if (empty($recipient_no)) {
            echo json_encode(['status' => 'error', 'message' => 'No valid recipient numbers']);
            return;
        }
        date_default_timezone_set('Australia/Brisbane');
        $current_time  = new DateTime();
        $form_date = new DateTime("08:00:00");
        $to_date = new DateTime("18:00:00");
        if ($current_time >= $form_date && $current_time <= $to_date) {
            $response = $this->skylline_sms->send_sms($recipient_no, $message, $channelName, $no_of_mcc);
            echo json_encode($response);
        } else {
            echo json_encode(["batch" => '', 'status' => 'error', 'message' => 'SMS can only be sent between 8:00 AM and 6:00 PM AEST']);
            return;
        }

        //  $response = $this->skylline_sms->send_sms($recipient_no, $message, $channelName, $no_of_mcc);
        //     echo json_encode($response);
    }
}

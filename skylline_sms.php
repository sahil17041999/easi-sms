<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Skylline_sms
{
    private $api_url = "http://remote.tyhtsf.com:51220/goip_post_sms.html";
    private $get_sms = 'http://remote.tyhtsf.com:51220/goip_get_sms.html';
    private $username = "root";
    private $password = "Tmed200Oksm5gW";
    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    // public function send_sms($to, $message, $channel_name, $no_of_mcc)
    // {

    //     $opt_out_message = "Reply NO to opt-out";
    //     $message .= "\n" . $opt_out_message;
    //     $tasks = [];
    //     $query = 'select last_tid from sms_logs order by last_tid desc limit 1';
    //     $log_data = $this->CI->db->query($query)->result_array();
    //     if (count($log_data) > 0) {
    //         $tid = $log_data[0]['last_tid'];
    //         $tid = $tid + 1;
    //         if (is_array($to) && count($to) > 1) {
    //             foreach ($to as $recipient) {
    //                 if ($this->is_opted_out($recipient)) {
    //                     continue;
    //                 }
    //                 $tasks[] = [
    //                     "tid" => $tid,
    //                     "to" => $recipient,
    //                     "sms" => $message
    //                 ];
    //                 $tid++;
    //             }
    //             if (empty($tasks)) {
    //                 return ["status" => "error", "message" => "SMS not sent. All recipients have opted out."];
    //             }
    //         } else {
    //             if ($this->is_opted_out($to[0])) {
    //                 return ["status" => "error", "message" => "SMS not sent. The recipient has opted-out."];
    //             }
    //             $tasks[] = [
    //                 "tid" => $tid,
    //                 "to" => $to[0],
    //                 "sms" => $message
    //             ];
    //         }
    //         $payload = [
    //             "type" => "send-sms",
    //             "task_num" => count($tasks),
    //             "tasks" => $tasks,
    //         ];
    //         $request = json_encode($payload);
    //         $ch = curl_init();
    //         curl_setopt($ch, CURLOPT_URL, $this->api_url . "?username={$this->username}&password={$this->password}");
    //         curl_setopt($ch, CURLOPT_POST, 1);
    //         curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    //         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //         curl_setopt($ch, CURLOPT_HTTPHEADER, [
    //             'Content-Type: application/json'
    //         ]);
    //         $response = curl_exec($ch);
    //         $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    //         if (curl_errno($ch)) {
    //             curl_close($ch);
    //             return [
    //                 "status" => "error",
    //                 "message" => "cURL Error: " . curl_error($ch)
    //             ];
    //         }
    //         curl_close($ch);
    //         if ($http_code == 200) {
    //             $response_data = json_decode($response, true);
    //             $last_tid = 0;
    //             if (!empty($response_data['status']) && is_array($response_data['status']) == true && count($response_data['status']) >= 1) {
    //                 $last_tid = max($response_data['status']);
    //             }
    //             $this->log_sms($to, $message, $tid, $request, $response_data, $channel_name, $no_of_mcc);

    //             if (isset($response_data['status'][0]['status']) && $response_data['status'][0]['status'] == '2 Port Unavailable') {
    //                 return [
    //                     "status" => "error",
    //                     "message" => "Failed to send SMS. Port unavailable. Please try again later."
    //                 ];
    //             }
    //             return $response_data;
    //         } else {
    //             return [
    //                 "status" => "error",
    //                 "message" => "Failed to send SMS. HTTP Code: $http_code"
    //             ];
    //         }
    //     } else {
    //         return [
    //             "status" => "error",
    //             "message" => "Failed to send SMS. Tid unavailable. Please try again."
    //         ];
    //     }
    // }

    public function send_sms($to, $message, $channel_name, $no_of_mcc, $batch_size = 1000)
    {
        if (isset($to[0]) && is_array($to[0])) {
            $to = array_map('reset', $to);
        }
        $total_contacts = count($to);
        $batches = array_chunk($to, $batch_size);
        $results = [];
        $batch_number = 1;
        foreach ($batches as $batch) {
            $opt_out_message = "Reply NO to opt-out";
            $message_with_opt_out = $message . "\n" . $opt_out_message;
            $tasks = [];
            $query = 'SELECT last_tid FROM sms_logs ORDER BY last_tid DESC LIMIT 1';
            $log_data = $this->CI->db->query($query)->result_array();
            $tid = $log_data[0]['last_tid'] ?? 0;
            $tid++;
            foreach ($batch as $recipient) {
                if ($this->is_opted_out($recipient)) {
                    continue;
                }
                $tasks[] = [
                    "tid" => $tid,
                    "to" => $recipient,
                    "sms" => $message_with_opt_out
                ];
                $tid++;
            }

            if (empty($tasks)) {
                $results[] = [
                    "batch" => $batch_number,
                    "status" => "error",
                    "message" => "All recipients in this batch have opted out."
                ];
                $batch_number++;
                continue;
            }

            $payload = [
                "type" => "send-sms",
                "task_num" => count($tasks),
                "tasks" => $tasks,
            ];
            $request = json_encode($payload);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->api_url . "?username={$this->username}&password={$this->password}");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                curl_close($ch);
                $results[] = [
                    "batch" => $batch_number,
                    "status" => "error",
                    "message" => "cURL Error: " . curl_error($ch)
                ];
                $batch_number++;
                continue;
            }
            curl_close($ch);

            if ($http_code == 200) {
                $response_data = json_decode($response, true);
                $this->log_sms($batch, $message_with_opt_out, $to, $tid, $request, $response_data, $channel_name, $no_of_mcc);
                $results[] = [
                    "batch" => $batch_number,
                    "status" => "success",
                    "message" => "SMS sent successfully.",
                    "response" => $response_data
                ];
            } else {
                $results[] = [
                    "batch" => $batch_number,
                    "status" => "error",
                    "message" => "Failed to send SMS. HTTP Code: $http_code"
                ];
            }

            $batch_number++;
        }

        return $results;
    }


    public function get_sms($sms_id = '', $sms_num = '')
    {

        $url = $this->get_sms . '?username=' . $this->username . '&password=' . $this->password;
        if (!empty($sms_id)) {
            $url .= '&sms_id=' . $sms_id;
        }
        if (!empty($sms_num)) {
            $url .= '&sms_num=' . $sms_num;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if ($response === false) {
            return 'Error fetching SMS: ' . curl_error($ch);
        }
        curl_close($ch);
        return $response;
    }

    private function log_sms($batch, $message, $to, $tid, $request, $response_data, $channel_name, $no_of_mcc)
    {
        if (is_array($message)) {
            $message = json_encode($message);
        }
        if (is_array($batch)) {
            $batchs = json_encode($batch);
        }
        $recipients = is_array($batch) ? implode(',', $batch) : $batch;
        $log_data = [
            'recipient' => $recipients,
            'request' => $request,
            'message' => $message,
            'last_tid' => $tid,
            'status' => $response_data['status'][0]['status'] ?? 'unknown',
            'response' => json_encode($response_data),
            'channel_name' => $channel_name,
            'no_of_mcc' => $no_of_mcc,
            'batach' => $batchs,
        ];
        $this->CI->db->insert('sms_logs', $log_data);
    }

    private function is_opted_out($phone_number)
    {
        $query = $this->CI->db->get_where('opt_out_numbers', ['phone_number' => $phone_number]);
        return $query->num_rows() > 0;
    }
}

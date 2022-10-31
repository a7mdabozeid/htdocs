<?PHP

class OziomaApiImpl
{
    private $user_name = ""; //set your user name here
    private $password = ""; //set your password here
    private $balance = 0.0;
    private $message = "";
    private $sender = "";
    private $recipient = "";
    private $message_id;
    private $status_msg = "";
    private $channel_id = 3;
    private $status_code = "";
    private $schedule_date = ""; //YYYY-MM-DD HH:MM format

    private $total_charged = 0.0;
    private $total_sent = 0;
    private $total_failed = 0;
    private $failed_nos = "";

    private $remote_op = '';

    //rtype = plain/json/xml

    //private $api_url = "http://localhost/OtimkpuSMS/api/sms_handler.php";
    private $api_url = "http://ozioma.chibex.net/api/sms_handler.php";

    public function __construct()
    {
    }

    private function get_user_name()
    {
        return $this->user_name;
    }

    private function get_password()
    {
        return $this->password;
    }

    public function set_message($message)
    {
        $this->message = $message;
    }

    public function get_message()
    {
        return $this->message;
    }

    public function set_sender($sender)
    {
        $this->sender = $sender;
    }

    public function get_sender()
    {
        return $this->sender;
    }

    public function set_recipient($recipient)
    {
        $this->recipient = $recipient;
    }

    public function get_recipient()
    {
        return $this->recipient;
    }

    public function set_schedule_date($schedule_date)
    {
        $this->schedule_date = $schedule_date;
    }

    public function get_schedule_date()
    {
        return $this->schedule_date;
    }

    public function get_balance()
    {
        return $this->balance;
    }

    public function get_status()
    {
        return $this->status_msg;
    }

    private function prepare_sms_params()
    {
        return array(
            'username'  => urlencode($this->get_user_name()),
            'password'  => urlencode($this->get_password()),
            'sender'    => urlencode($this->get_sender()),
            'message'   => urlencode($this->get_message()),
            'recipient' => urlencode($this->get_recipient()),
            'remoteOp'  => urlencode($this->remote_op)
        );
    }

    public function check_balance()
    {
        $this->remote_op = 'bal';
        $params          = array(
            'username' => urlencode($this->get_user_name()),
            'password' => urlencode($this->get_password()),
            'balance'  => urlencode(true),
            'remoteOp' => urlencode($this->remote_op)
        );

        //send sms and retrieve response
        $response = $this->execute_curl($params);

        //process response
        $this->parse_response($response);
    }

    public function send()
    {
        $this->remote_op = 'snd';
        //encode each parameter value and present array
        $params = $this->prepare_sms_params();

        //send sms and retrieve response
        $response = $this->execute_curl($params);

        //process response
        $this->parse_response($response);
    }

    public function schedule()
    {
        $this->remote_op = 'sdl';
        //encode each parameter value and present array
        $params = $this->prepare_sms_params();

        //add schedule variable to the request
        $params['schedule'] = urlencode($this->get_schedule_date());

        //send sms and retrieve response
        $response = $this->execute_curl($params);

        //process response
        $this->parse_response($response);
    }

    private function parse_response($response)
    {
        //convert response from json to assoc array
        $response_arr = json_decode(stripcslashes($response), true);

        if ($response_arr !== NULL) {
            //get status code and status message from response json and set local variable
            $this->status_code = $response_arr['statusCode'];
            $this->status_msg  = $response_arr['statusMessage'];

            //check if login failed or incomplete parameters
            if ($this->status_code == "000010" || $this->status_code == "000020")
                return;

            //parameters were complete and login was successful

            //is user checking account balance
            if ($this->remote_op == 'bal') {
                $this->balance = floatval($response_arr['balance']);
            } else if ($this->remote_op == 'snd') //test for send operation
            {
                if ($this->status_code == "000040")//sent successful
                {
                    $this->total_charged = floatval($response_arr['charged']);
                    $this->total_failed  = floatval($response_arr['failed']);
                    $this->failed_nos    = $response_arr['failedNos'];
                    $this->message_id    = $response_arr['messageId'];
                }
            } else if ($this->remote_op == 'msg') //fetch message by id
            {
                if ($this->status_code != "000100")//is message id correct
                {
                    $this->total_charged = floatval($response_arr['charged']);
                    $this->total_failed  = floatval($response_arr['failed']);
                    $this->failed_nos    = $response_arr['failedNos'];
                    $this->message_id    = $response_arr['messageId'];
                    $this->message       = $response_arr['message'];
                    $this->recipient     = $response_arr['recipient'];
                    $this->sender        = $response_arr['sender'];
                }
            }
        } else {
            //if json_decode() function cannot parse response, it means that there is remote server error
            $this->status_code = '000060';
            $this->status_msg  = 'Server Error';
        }

    }

    public function fetch_message($id)
    {
        $this->remote_op = 'msg';
        $params          = array(
            'username'  => urlencode($this->get_user_name()),
            'password'  => urlencode($this->get_password()),
            'messageId' => urlencode($id),
            'remoteOp'  => urlencode($this->remote_op)
        );

        //retrieve message by id
        $response = $this->execute_curl($params);

        //process response
        $this->parse_response($response);
    }

    public function resend_failed_nos()
    {
        //set failed numbers as recipients
        $this->set_recipient($this->failed_nos);

        //send back the message
        $this->send();
    }

    private function execute_curl($params)
    {
        $encoded_params = "";
        foreach ($params as $key => $value) {
            $encoded_params .= $key . '=' . $value . '&';
        }
        rtrim($encoded_params, '&');

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_POST, count($params));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded_params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);

        return rtrim($result, '1');
    }
}
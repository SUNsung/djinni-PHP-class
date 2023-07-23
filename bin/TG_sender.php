<?php

class TG_sender{
    protected array $content;
    protected ?array $inline_button=null;
    protected ?array $line_button=null;

    public function __construct(private string $url_api){}
    public function validate(string $string):string{
        $a1 = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
        $a2 = ['\_', '\*', '\[', '\]', '\(', '\)', '\~', '\`', '\>', '\#', '\+', '\-', '\=', '\|', '\{', '\}', '\.', '\!'];
        return str_replace($a1, $a2, $string);
    }

    public function add_button(string $text, int $x=0, int $y=0):void{
        if($this->line_button === null) $this->line_button = [];

        if($this->line_button[$x] === null) $this->line_button[$x] = [];
        $this->line_button[$x][$y] = mb_substr($text, 0, 40);;
    }
    public function add_inline_urlQuery(string $text, string $url, int $x=0, int $y=0):void{

        if($this->inline_button === null) $this->inline_button = [];
        if($this->inline_button[$x] === null) $this->inline_button[$x] = [];

        $this->inline_button[$x][$y] = [
            "text" => mb_substr($text, 0, 40),
            "url" => mb_substr($url, 0, 256)
        ];
    }
    public function add_inline_switchQuery(string $text, string $share_content, int $x=0, int $y=0):void{

        if($this->inline_button === null) $this->inline_button = [];
        if($this->inline_button[$x] === null) $this->inline_button[$x] = [];

        $this->inline_button[$x][$y] = [
            "text" => mb_substr($text, 0, 40),
            "switch_inline_query" => $share_content
        ];
    }
    public function add_inline_button(string $text, string $callback, int $x=0, int $y=0):void{

        if($this->inline_button === null) $this->inline_button = [];
        if($this->inline_button[$x] === null) $this->inline_button[$x] = [];

        $this->inline_button[$x][$y] = [
            "text" => mb_substr($text, 0, 40),
            "callback_data" => mb_substr($callback, 0, 64)
        ];
    }

    public function send_msg(int $chat_id, string $text, bool $protect=true, bool $notification=true):object{
        $this->content = [];

        $this->content["chat_id"] = $chat_id;
        $this->content["text"] = base64_encode($text);
        $this->content["hash"] = hash("adler32", $text);
        $this->content["protect"] = $protect;
        $this->content["notification"] = $notification;

        if($this->line_button !== null) $this->content["line_button"] = $this->line_button;
        if($this->inline_button !== null) $this->content["inline_button"] = $this->inline_button;

        return $this->send_req();
    }
    protected function send_req():object{
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->content));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_URL, $this->url_api);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);


        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
         unset($this->content);


        $return = (object)[];
        $return->code = $info["http_code"];
        $return->scheme = $info["scheme"];
        $return->contentType = $info["content_type"];
        $return->ip = $info["primary_ip"];
        $return->port = $info["primary_port"];
        $return->body = $result;

        return $return;
    }
}
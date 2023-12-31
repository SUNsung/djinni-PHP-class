<?php require_once __DIR__."/bin/autoload.php";

$DJ = new \djinni\Start(mail: sysConstants::$user_email, password: sysConstants::$user_pass);

//$DJ->logout();

if(1) if(!$DJ->is_auth()){
    $rez = $DJ->auth();
    if (!$rez) sys::print(code: 401, title: "Unauthorized");
}


$DJ->start_search(pages: 2)
    ->add_specialization("PHP")
    ->add_english("no_english")->add_english("basic")->add_english("pre")
    ->add_salaryFrom("2500")
    ->add_employment("remote");

$search = $DJ->load_jobsBySearch();
sys::print($search, "Search");

//Финальна заглушка
sys::print(code: 400, title: "Bad Request [global]", msg: "Rout not catch");

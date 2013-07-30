echo __FILE__, ':', __FUNCTION__, '('.implode(', ', func_get_args()).') @ line ', __LINE__, "\n";

//Die var dump
function dvd() {
    $backtrace = debug_backtrace();
    $last = array_pop($backtrace);

    echo 'die called from '.$last['file'].' line '.$last['line'], "\n";

    foreach(func_get_args() as $arg) {
        echo var_export($arg, true), "\n";
    }

    die();
}

<?php

// Sure, let's take a class's methods and make them all global
$methods = get_class_methods('SomeClass');
foreach($methods as $method) {
	eval("function $method() {
		return call_user_func_array(array('SomeClass', '$method'), func_get_args());
	}");
}

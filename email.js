// Had a login form coming from an api that included fields for email input
// with type="text". This snippet gave all the email inputs the proper type
// to change to the email keyboard layout on mobile devices.

$('.email').each(function () {
    var html = $(this).wrap('<div>').parent().html().replace(/type=\"text\"/, 'type="email"');
    $(html).insertAfter(this).prev().remove();
});

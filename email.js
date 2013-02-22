$('.email').each(function () {
    var html = $(this).wrap('<div>').parent().html().replace(/type=\"text\"/, 'type="email"');
    $(html).insertAfter(this).prev().remove();
});

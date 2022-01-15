function Blog() {
    this.active = window.location.pathname.indexOf("blog") > -1;

    this.init = function () {
        /* Check for summernote */
        var scripts = [].slice.call(document.getElementsByTagName("script"));
        if (scripts.filter(e => e.src.indexOf("summernote") >= 0).length > 0 && blog.active) {
            // summernote.focus
            $('.editable').on('summernote.blur', function (element) {
                $("#save").show();
                var value = $(element.currentTarget).summernote('code'),
                    key = element.currentTarget.id;

                blog.store(key, value, false);
            });
        }
    }

    this.save = function (elem, reload) {
        // Let's save this element.
        $("#save").show();

        var key = elem.id.replace('_field',''),
            value = elem.value.replace('_field','');

        blog.store(key, value, reload);
    };

    this.store = function (key, value, reload) {
        var page = decodeURI(window.location.pathname.split("/").pop());
        
        $.ajaxSetup({async: false});
        $.post(rootURL + "/plugins/simple-blog/save.php", {key: key, value: value, page: page, token: token})
            .done(function (data) {
                if (data) alert(data);
                $("#save").hide();

                // I really don't like this, but it's default behaviour on cms pages so will honor this.
                if (reload) history.go(0);
            });
    }

    this.new = function () {

        // Ask for name
        var name = prompt("New post title");
        if (name && name.trim() != "") {

            // Let's save this element.
            $("#save").show();
            $.ajaxSetup({async: false});
            $.post(rootURL + "/plugins/simple-blog/new.php", {page: name, token: token})
                .done(function (data) {
                    window.location.href = data;
                    $("#save").hide();
                });

        }

    };

}

window.blog = new Blog();
window.addEventListener('load', blog.init);

// Overwrite default save method for editors other than summernote:

window.addEventListener('click', function (e) {
    const editableText = document.querySelectorAll('div.editText.editTextOpen:not(:hover) textarea');

    editableText.forEach((editableElement) => {
        e.stopPropagation();
        e.preventDefault();

        $("#save").show();
        blog.save(editableElement, true);
    });
});

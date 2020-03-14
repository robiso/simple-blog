function Blog() {
    this.active = window.location.pathname.indexOf("blog") > -1;

    this.init = function() {
        /* Check for summernote */
        var scripts = [].slice.call(document.getElementsByTagName("script"));
        if(scripts.filter(e => e.src.indexOf("summernote") >= 0).length > 0 && blog.active) {
            // summernote.focus
            $('.editable').on('summernote.blur', function(element) {
              var value = $(element.currentTarget).summernote('code'),
                  key = element.currentTarget.classList[0];

              blog.store(key, value, false);
            });
        } else {
            /* Attach to admin elements */
            [].forEach.call(document.getElementsByClassName("blog-editable"), elem => {
                elem.onclick = () => {
                    // Make sure 2nd click does nothing
                    elem.onclick = () => {};

                    // Capture html
                    var html = elem.innerHTML;

                    // Create textarea
                    var textarea = document.createElement("textarea");
                    textarea.innerHTML = html;
                    textarea.className = elem.classList[0];
                    textarea.onblur = () => { blog.save(textarea, true); };

                    // Reset elem
                    elem.innerHTML = "";

                    // Add textarea
                    elem.appendChild(textarea);

                    // (Bad) auto resize foo
                    textarea.onkeydown = () => {
                        textarea.style.height = 'auto';
                        textarea.style.height = (textarea.scrollHeight + 100) + 'px';
                    };
                    textarea.style.height = 'auto';
                    textarea.style.height = (textarea.scrollHeight + 100) + 'px';
                };
            });
        }
    }

    this.save = function(elem, reload) {
        // Let's save this element.
        $("#save").show();

        var key = elem.classList[0],
            value = elem.value;

        blog.store(key, value, reload);

    };

    this.store = function(key, value, reload) {
        var page = decodeURI(window.location.pathname.split("/").pop());

        $.post( rootURL + "/plugins/simple-blog/save.php", { key: key, value: value, page: page, token: token })
            .done(function( data ) {
                if(data) alert(data);
                $("#save").hide();

                // I really don't like this, but it's default behaviour on cms pages so will honor this.
                if(reload) history.go(0);
            });
    }

    this.new = function() {

        // Ask for name
        var name = prompt("New post title");
        if(name && name.trim() != "") {

            // Let's save this element.
            $("#save").show();

            $.post( rootURL + "/plugins/simple-blog/new.php", { page: name, token: token })
                .done(function( data ) {
                    window.location.href = data;
                    $("#save").hide();
                });

        }

    };

}

window.blog = new Blog();
window.addEventListener('load', blog.init);

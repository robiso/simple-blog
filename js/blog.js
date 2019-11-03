function Blog() {

    this.save = function(elem) {

        // Let's save this element.
        $("#save").show();

        var key = elem.classList[0],
            value = elem.innerHTML,
            page = decodeURI(window.location.pathname.split("/").pop());

        $.post( rootURL + "/plugins/simple-blog/save.php", { key: key, value: value, page: page, token: token })
            .done(function( data ) {
                if(data) alert(data);
                $("#save").hide();
            });



    };

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

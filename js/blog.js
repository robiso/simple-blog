function Blog() {

    this.save = function(elem) {

        // Let's save this element.
        $("#save").show();

        var key = elem.classList[0],
            value = elem.innerHTML,
            page = window.location.pathname.split("/").pop();

        console.log(key, value);
        $.post( "../plugins/simpleblog/save.php", { key: key, value: value, page: page })
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

            $.post( "plugins/simpleblog/new.php", { page: name })
                .done(function( data ) {
                    window.location.href = data;
                    $("#save").hide();
                });

        }

    };

}

window.blog = new Blog();

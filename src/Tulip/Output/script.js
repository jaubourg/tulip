Tulip = {
	toggle: function( id ) {
		element = document.getElementById( id );
		element.style.display = ( element.style.display == "none" ) ? "block" : "none";
	},
	open: function( test ) {
		var tmp = "" + document.location;
		tmp = tmp.replace( /\?.*$/ , "" );
		document.location = tmp + "?" + escape(test);
	}
};
jQuery( function( ) {
	var $form = jQuery( ".table-results-form-create" ) ;

	jQuery( ".table-results-cell[data-name]" ).on( "click" , function( ) {
		var $self = jQuery( this ) ;
		var $row = $self.parents( ".table-results-row:first" ) ;
		var $cells = $row.find( ".table-results-cell" ) ;
		$cells.each( function( ) {
			var $cell = jQuery( this ) ;
			var $name = $cell.data( "name" ) ;
			var $value = $cell.data( "value" ) ;

			$form.find( ":input[name=" + $name + "]" ).val( $value ) ;
		} ) ;

		$form.find( ":input[name=action]" ).val( $row.data( "action" ) ) ;

		return false ;
	} ) ;

	jQuery( ".table-results-delete" ).on( "click" , function( $evt ) {
		try {
			$evt.stopPropagation( ) ;
		} catch( $exception ) {
			$evt.cancelBubble = true ;
		}

		return confirm( window.$message.ask.delete ) ;
	} ) ;

	jQuery( ".table-results-row .table-results-cell[data-key=pri]" ).on( "click", function( ) {
		
	} ) ;
} ) ;
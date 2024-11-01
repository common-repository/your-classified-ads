jQuery(document).ready(function($){
	//ADVANCED SEARCH LISTS
        var el=$("ul.expandable ul");

	if (el.length>0) {
		el.collapsibleCheckboxTree({
			checkParents : false,
			checkChildren : true,
			uncheckChildren : true,
			initialState : 'default'

		});
	}
})

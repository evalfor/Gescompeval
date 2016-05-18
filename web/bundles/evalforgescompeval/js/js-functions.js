/*
 * Change values of a form when you click over a select
 */
/*function changeValues(select_id, attr1_id, attr2_id, attr3_id)
{
	var seleccion = document.getElementById(select_id);

	// If default value is selected, empty the fields
	if (seleccion.options[seleccion.selectedIndex].value == 0) {
		document.getElementById(attr1_id).value = "";
		document.getElementById(attr2_id).value = "";
		document.getElementById(attr3_id).value = "";
	}
	// If an element is selected, the suitable values are loaded
	else{
		document.getElementById(attr1_id).value = seleccion.options[seleccion.selectedIndex].value;
		document.getElementById(attr2_id).value = seleccion.options[seleccion.selectedIndex].title;
		document.getElementById(attr3_id).value = seleccion.options[seleccion.selectedIndex].id;
	}
}*/

/**
 * Change hidden id of a form when you click over a select
 */
function changeId(select_id, id_id)
{
	var seleccion = document.getElementById(select_id);

	// If default value is selected, empty the fields
	if (seleccion.options[seleccion.selectedIndex].value == 0) {
		document.getElementById(id_id).value = "";
	}
	// If an element is selected, the suitable values are loaded
	else{
		document.getElementById(id_id).value = seleccion.options[seleccion.selectedIndex].value;
	}
}

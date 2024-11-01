
function afs_add_field() { 
	
	afs_fields++;
	r=afs_fields-1;
	
	var afs_row = `
	<select id="afs_type${r}" name="afs_type[${r}]" >
	  <option value="">Select type</option>
	  <option value="img">Image</option>
	  <option value="js">Javascript</option>
	  <option value="ifr">Iframe</option>
	</select>
	<input id="afs_url${r}" name="afs_url[${r}]" type="url" placeholder="https://example.com/pixel.php?amount=[order_total]">
	<br><br>
	`;

	 document.getElementById("afs_dynamic_rows").insertAdjacentHTML( "beforeend", afs_row );												
}
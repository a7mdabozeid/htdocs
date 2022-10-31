document.addEventListener("DOMContentLoaded", function() {

	var wooqr_img = document.createElement('img');
	wooqr_img.src =
		wooqr['qr_options']['image'];

	wooqr['qr_options']['image'] = wooqr_img;

	var myHeaders = new Headers();
	myHeaders.append("X-WP-Nonce", wooqr.wp_rest);

	var requestOptions = {
		method: 'GET',
		headers: myHeaders,
		redirect: 'follow'
	};



	// Selecting The Container.
	const container = document.querySelector('#wooqr-data');
	createProductItem();
	window.addEventListener('scroll',()=>{
		const {scrollHeight,scrollTop,clientHeight} = document.documentElement;
		if(scrollTop + clientHeight > scrollHeight - 5){
			//setTimeout(createProductItem(),100);
		}
	});


	// It append it to the container.
	function createProductItem(pro_page = 1){

		fetch(wooqr.wp_rest_url+"wc/v3/products?per_page=30&orderby=id&order=asc&page="+pro_page, requestOptions)
			.then(
				function(response) {
					if (response.status !== 200) {
						document.getElementById("wooqr-status").innerHTML('Looks like there was a problem. Status Code: ' +
							response.status);
						return;
					}

					response.json().then(function(data) {



						// console.log(response.headers.get('X-WC-Webhook-ID'));
						//  console.log(response.headers.get('X-WP-Total'));
						// console.log(response.headers.get('X-WP-TotalPages'));
						// console.log(response.headers.get('Content-Type'));
						// console.log(response.headers.get('Date'));
						// console.log(response.status);
						// console.log(response.statusText);
						// console.log(response.type);
						// console.log(response.url);
						data.forEach(p => {
							let productItem = document.createElement('li');
							// let iCat = p.categories.join(" ");
							// console.log(p);
							if(p.wooqr_code != "" && typeof p.wooqr_code != "undefined") {
								var wooqr_image = wooqr.wooqr_folder+p.wooqr_code;

							}
							else {
								var wooqr_image = wooqr.wooqr_plugin+"assets/admin/images/no_qr.svg";

							}
							productItem.setAttribute("id", "result_"+p.id);
							productItem.setAttribute("data-proid", p.id);
							productItem.className = 'result pro-item product-grid-item product_qrcode_content ptype-'+p.type+' ';
							let iQR = "<div class='iqr-image'></div>";
							let iId = "<span class='iid'><a href='"+ document.location.origin +"/wp-admin/post.php?post="+p.id+"&action=edit'>#" +p.id+ "</a> - "+p.type+"</span>";
							let iName = "<div class='iname bulk_product-qr-code-title'>" +p.name+ "</div>";
							let iPrice = "<div class='iprice bulk_product-qr-code-price'>" +p.price_html+ "</div>";
							let iaction = "<div class='wooqr_actions'><div class='button button-primary print-qr dashicons-before dashicons-print' data-product_id='"+p.id+"'>Print</div></div>";

							if(p.type == "variable") {
								iaction = "<div class='wooqr_actions'><div class='button button-primary print-qr dashicons-before dashicons-print' data-product_id='"+p.id+"'>Print</div><div class='button button-primary show-qr-variations dashicons-before dashicons-print' data-product_id='"+p.id+"' data-product_title='"+p.name+"'>Show Variations</div></div>";

							}


							// content
							wooqr['qr_options'].text = p.permalink;


							productItem.innerHTML = iQR + iId + iName + iPrice + iaction;
							//   Appending the post to the container.

							productItem.querySelector(".iqr-image").appendChild(kjua(wooqr['qr_options']));

							container.appendChild(productItem);


						});
						pro_page += 1;
						//console.log(pro_page);
						if( (pro_page <= response.headers.get('X-WP-TotalPages') )) {
							setTimeout(createProductItem(pro_page),100);
						}
						else {
							document.getElementById("wooqr_loader").style.display = "none";
						}

					});
				}
			)
			.catch(function(err) {
				document.getElementById("wooqr-status").innerHTML('Fetch Error :-S', err);
			});

	}


	document.addEventListener('click',function(e){

		if(e.target && e.target.classList.contains("show-qr-variations")){


			/* document.querySelectorAll('.qr-variations').forEach(function(a) {
				a.remove()
			}) */
			var pid = e.target.getAttribute('data-product_id');
			var pname = e.target.getAttribute('data-product_title');
			e.target.classList.remove('show-qr-variations');
			// console.log(pid);
			let vpro_page = 1;
			wooqr_fetch_variations(pid, pname, vpro_page);

			function wooqr_fetch_variations(pid, pname, vpro_page = 1) {
				fetch(wooqr.wp_rest_url+"wc/v3/products/"+pid+"/variations/?per_page=30&orderby=id&order=asc&page="+vpro_page, requestOptions)
					.then(
						function(response) {
							if (response.status !== 200) {
								document.getElementById("wooqr-status").innerHTML('Looks like there was a problem. Status Code: ' +
									response.status);
								return;
							}

							response.json().then(function(data) {

								// console.log(data);
								// console.log(response.headers.get('X-WC-Webhook-ID'));
								//  console.log(response.headers.get('X-WP-Total'));
								// console.log(response.headers.get('X-WP-TotalPages'));
								// console.log(response.headers.get('Content-Type'));
								// console.log(response.headers.get('Date'));
								// console.log(response.status);
								// console.log(response.statusText);
								// console.log(response.type);
								// console.log(response.url);
								data.forEach(p => {
									let productItem = document.createElement('li');

									// console.log(p);
									let text = "";
									for (let i in p.attributes) {
										text += p.attributes[i].name + ": " + p.attributes[i].option + "<br> ";
									}

									if(p.wooqr_code != "" && typeof p.wooqr_code != "undefined") {
										var wooqr_image = wooqr.wooqr_folder+p.wooqr_code;

									}
									else {
										var wooqr_image = wooqr.wooqr_plugin+"assets/admin/images/no_qr.svg";

									}
									if(p.price){
										var vprice = wooqr.woo_currency + "" + p.price;
									} else {
										var vprice = "price not set";
									}
									productItem.setAttribute("id", "result_"+p.id);
									productItem.setAttribute("data-proid", p.id);
									productItem.className = 'result pro-item product-grid-item product_qrcode_content qr-variations';
									let iQR = "<div class='iqr-image'></div>";
									let iId = "<span class='iid'><a href='"+ document.location.origin +"/wp-admin/post.php?post="+pid+"&action=edit'>#" +p.id+ "</a> - variation</span>";
									let iName = "<div class='iname bulk_product-qr-code-title'>" +pname+ "<div class='vproduct-attrs'>"+text+"</div></div>";
									let iPrice = "<div class='iprice bulk_product-qr-code-price'>" +vprice+ "</div>";
									let iaction = "<div class='wooqr_actions'><div class='button button-primary print-qr dashicons-before dashicons-print' data-product_id='"+p.id+"'>Print</div></div>";



									wooqr['qr_options'].text = p.permalink;

									productItem.innerHTML = iQR + iId + iName + iPrice + iaction;
									//   Appending the post to the container.

									productItem.querySelector(".iqr-image").appendChild(kjua(wooqr['qr_options']));


									function insertAfter(referenceNode, newNode) {
										referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
									}

									let currentli = document.getElementById("result_"+pid);
									insertAfter(currentli, productItem);

									// container.appendChild(productItem);


								});
								vpro_page += 1;
								//console.log(pro_page);
								if( (vpro_page <= response.headers.get('X-WP-TotalPages') )) {
									setTimeout(wooqr_fetch_variations(pid, vpro_page),100);
								}
								else {
									document.getElementById("wooqr_loader").style.display = "none";
								}
								e.target.parentElement.removeChild(e.target);
							});
						}
					)
					.catch(function(err) {
						document.getElementById("wooqr-status").innerHTML('Fetch Error :-S', err);
					});

			}

		}
	});
});

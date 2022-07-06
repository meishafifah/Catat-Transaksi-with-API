var var_global = '';
var interval_global = '';

function callPage(file_html, param = '') {

	clearInterval(interval_global);

	if (param != '') {
		var_global = param;
	}

	$.ajax(file_html, {
		type: 'GET',
		success: function (data, status, xhr) {
			$('#kontenHTML').html(data);

			var judul = '';

			if (file_html == 'list_transaksi.html') {
				judul = 'List Tranksaksi';
			} else if (file_html == 'login.html') {
				judul = 'Form Login';
			} else if (file_html == 'laporan.html') {
				judul = 'Laporan';
			} else {
				judul = 'Form Tranksaksi';
			}

			$('title').html(judul);
			$('.navbar-brand').html('Halaman '+judul);
			
		},
		error : function (jqXHR, textStatus, errorMsg) {
			$('#kontenHTML').html('Error : ' + errorMsg);
			$('title').html('Error');
		}
	});
}



var base_url = 'http://127.0.0.1:8000/';
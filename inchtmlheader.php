<!doctype html><html lang="en">
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="refresh" content="900; url=logout.php">
  <title>My Data</title>
  <meta content='width=device-width, initial-scale=1, maximum-scale=1' name='viewport'/>
  <script src="https://accounts.google.com/gsi/client" async defer></script>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css"/>
 
<script type="text/javascript" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/date-fns/1.30.1/date_fns.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

<script>
//Global variables

//Functions
function switchview(prefcashflowview) {
	
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
	  if (this.readyState == 4 && this.status == 200) {
		if(this.responseText) {
			if (this.responseText != 'Success') { alert(this.responseText); };
		}
		window.location.reload();
		return;
	  }
	};
	xmlhttp.open("GET","sessionvarupdate.php?prefcashflowview="+prefcashflowview,true);
	xmlhttp.send();
}

function sqlitemdelete(anotheritemid,deletetype) {
	
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
	  if (this.readyState == 4 && this.status == 200) {
		if(this.responseText) {
			if (this.responseText != 'Success') { alert(this.responseText); };
		}
		window.location.reload();
		return;
	  }
	};
	xmlhttp.open("GET","sqlitemdelete.php?itemid="+anotheritemid+"&deletetype="+deletetype,true);
	xmlhttp.send();
}
function sqlitemupdatepaid(itemid,itemaccountfrom,itemaccountto,itemamount) {
	
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
	  if (this.readyState == 4 && this.status == 200) {
		if(this.responseText) {
			if (this.responseText != 'Success') { alert(this.responseText); };
		}
		window.location.reload();
		return;
	  }
	};
	xmlhttp.open("GET","sqlitemupdatepaid.php?itemid="+itemid+"&itemaccountfrom="+itemaccountfrom+"&itemaccountto="+itemaccountto+"&itemamount="+itemamount,true);
	xmlhttp.send();
}
function switchrecur(option) {
	switch (option) {
		case '0':
			//None, so hide all the divs
			document.getElementById('repeattype').style.display='none';
		break;
		case '2':
			//Weekly, hide everything else
			document.getElementById('repeattype').style.display='block';
			document.getElementById('repeattype').innerHTML='<div class="form-floating mb-3"><input type="number" class="form-control" id="itemparentrepeatfreq" value="1" min="1" max="10" placeholder="Every x weeks"><label for="itemparentrepeatfreq">Every x weeks</label></div><div class="form-floating mb-3"><input type="date"  class="form-control" id="itemparentrepeatdate" value="2055-08-28" placeholder="Until"><label for="itemparentrepeatdate">Until</label></div>';
		break;
		case '3':
			//Monthly, hide everything else
			document.getElementById('repeattype').style.display='block';
			document.getElementById('repeattype').innerHTML='<div class="form-floating mb-3"><input type="number" class="form-control" id="itemparentrepeatfreq" value="1" min="1" max="10" placeholder="Every x months"><label for="itemparentrepeatfreq">Every x months</label></div><div class="form-floating mb-3"><input type="date"  class="form-control" id="itemparentrepeatdate" value="2055-08-28" placeholder="Until"><label for="itemparentrepeatdate">Until</label></div>';
		break;
		case '4':
			//Annually, hide everything else
			document.getElementById('repeattype').style.display='block';
			document.getElementById('repeattype').innerHTML='<div class="form-floating mb-3"><input type="number" class="form-control" id="itemparentrepeatfreq" value="1" min="1" max="10" placeholder="Every x years"><label for="itemparentrepeatfreq">Every x years</label></div><div class="form-floating mb-3"><input type="date"  class="form-control" id="itemparentrepeatdate" value="2055-08-28" placeholder="Until"><label for="itemparentrepeatdate">Until</label></div>';
		break;
	}
}
function switchrecuredit(option) {
	switch (option) {
		case '0':
			//None, so hide all the divs
			document.getElementById('erepeattype').style.display='none';
		break;
		case '2':
			//Weekly, hide everything else
			document.getElementById('erepeattype').style.display='block';
			document.getElementById('erepeattype').innerHTML='<div class="form-floating mb-3"><input type="number" class="form-control" id="eitemparentrepeatfreq" value="1" min="1" max="10" placeholder="Every x weeks"><label for="eitemparentrepeatfreq">Every x weeks</label></div><div class="form-floating mb-3"><input type="date"  class="form-control" id="eitemparentrepeatdate" value="2055-08-28" placeholder="Until"><label for="eitemparentrepeatdate">Until</label></div>';
		break;
		case '3':
			//Monthly, hide everything else
			document.getElementById('erepeattype').style.display='block';
			document.getElementById('erepeattype').innerHTML='<div class="form-floating mb-3"><input type="number" class="form-control" id="eitemparentrepeatfreq" value="1" min="1" max="10" placeholder="Every x months"><label for="eitemparentrepeatfreq">Every x months</label></div><div class="form-floating mb-3"><input type="date"  class="form-control" id="eitemparentrepeatdate" value="2055-08-28" placeholder="Until"><label for="eitemparentrepeatdate">Until</label></div>';
		break;
		case '4':
			//Annually, hide everything else
			document.getElementById('erepeattype').style.display='block';
			document.getElementById('erepeattype').innerHTML='<div class="form-floating mb-3"><input type="number" class="form-control" id="eitemparentrepeatfreq" value="1" min="1" max="10" placeholder="Every x years"><label for="eitemparentrepeatfreq">Every x years</label></div><div class="form-floating mb-3"><input type="date"  class="form-control" id="eitemparentrepeatdate" value="2055-08-28" placeholder="Until"><label for="eitemparentrepeatdate">Until</label></div>';
		break;
	}
}
function sqliteminsert() {
	
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
	  if (this.readyState == 4 && this.status == 200) {
		if(this.responseText) {
			if (this.responseText != 'Success') { alert(this.responseText); };
		}
		window.location.reload();
		return;
	  }
	};
	var itemparentname = document.getElementById('itemparentname').value;
	var itemparentamount = document.getElementById('itemparentamount').value;
	var itemaccountfrom = document.getElementById('itemaccountfrom').value;
	var itemaccountto = document.getElementById('itemaccountto').value;
	var itemparentdate = document.getElementById('itemparentdate').value;
	var itemparentrepeattype = document.getElementById('itemparentrepeattype').value;
	var itemparentrepeatfreq = document.getElementById('itemparentrepeatfreq').value;
	var itemparentrepeatdate = document.getElementById('itemparentrepeatdate').value;
	
	var url = "sqliteminsert.php?itemparentname="+itemparentname+"&itemparentamount="+itemparentamount+"&itemaccountfrom="+itemaccountfrom+"&itemaccountto="+itemaccountto+"&itemparentdate="+itemparentdate+"&itemparentrepeattype="+itemparentrepeattype+"&itemparentrepeatfreq="+itemparentrepeatfreq+"&itemparentrepeatdate="+itemparentrepeatdate;
	
	xmlhttp.open("GET",url,true);
	xmlhttp.send();
}
function sqlitemedit() {
	
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
	  if (this.readyState == 4 && this.status == 200) {
		if(this.responseText) {
			if (this.responseText != 'Success') { alert(this.responseText); };
		}
		window.location.reload();
		return;
	  }
	};
	//Delete the old one first
	var itemid = document.getElementById('emodalitemid').value;
	sqlitemdelete(itemid,'2');
	
	//Now add it as a new item
	var itemparentname = document.getElementById('eitemparentname').value;
	var itemparentamount = document.getElementById('eitemparentamount').value;
	var itemaccountfrom = document.getElementById('eitemaccountfrom').value;
	var itemaccountto = document.getElementById('eitemaccountto').value;
	var itemparentdate = document.getElementById('eitemparentdate').value;
	var itemparentrepeattype = document.getElementById('eitemparentrepeattype').value;
	var itemparentrepeatfreq = document.getElementById('eitemparentrepeatfreq').value;
	var itemparentrepeatdate = document.getElementById('eitemparentrepeatdate').value;
	
	var url = "sqliteminsert.php?itemparentname="+itemparentname+"&itemparentamount="+itemparentamount+"&itemaccountfrom="+itemaccountfrom+"&itemaccountto="+itemaccountto+"&itemparentdate="+itemparentdate+"&itemparentrepeattype="+itemparentrepeattype+"&itemparentrepeatfreq="+itemparentrepeatfreq+"&itemparentrepeatdate="+itemparentrepeatdate;
	
	xmlhttp.open("GET",url,true);
	xmlhttp.send();
}
function sqlaccountupdatecolour(accountid,accountcolour) {
	
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
	  if (this.readyState == 4 && this.status == 200) {
		if(this.responseText) {
			if (this.responseText != 'Success') { alert(this.responseText); };
		}
		window.location.reload();
		return;
	  }
	};
	xmlhttp.open("GET","sqlaccountupdatecolour.php?accountid="+accountid+"&accountcolour="+accountcolour.substring(1),true);
	xmlhttp.send();
}
function updatePrefsShowFavs(checked) {
	
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
	  if (this.readyState == 4 && this.status == 200) {
		if(this.responseText) {
			if (this.responseText != 'Success') { alert(this.responseText); };
		}
		window.location.reload();
		return;
	  }
	};
	if (checked == 1 ) {
		xmlhttp.open("GET","sqluserupdateprefshowfavs.php?checked=1&favs=0",true);
	} else {
		xmlhttp.open("GET","sqluserupdateprefshowfavs.php?checked=0&favs=0",true);
		
	}
	xmlhttp.send();
}
function updatePrefsShowFavsAssets(checked) {
	
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
	  if (this.readyState == 4 && this.status == 200) {
		if(this.responseText) {
			if (this.responseText != 'Success') { alert(this.responseText); };
		}
		window.location.reload();
		return;
	  }
	};
	if (checked == 1 ) {
		xmlhttp.open("GET","sqluserupdateprefshowfavs.php?checked=1&favs=1",true);
	} else {
		xmlhttp.open("GET","sqluserupdateprefshowfavs.php?checked=0&favs=1",true);
		
	}
	xmlhttp.send();
}
function sqlitemupdateaccountfrom(itemparentid,itemaccountfrom) {
	
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
	  if (this.readyState == 4 && this.status == 200) {
		if(this.responseText) {
			if (this.responseText != 'Success') { alert(this.responseText); };
		}
		window.location.reload();
		return;
	  }
	};
	xmlhttp.open("GET","sqlitemupdateaccount.php?itemparentid="+itemparentid+"&itemaccountfrom="+itemaccountfrom,true);
	xmlhttp.send();
}
function sqlitemupdateaccountto(itemparentid,itemaccountto) {
	
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
	  if (this.readyState == 4 && this.status == 200) {
		if(this.responseText) {
			if (this.responseText != 'Success') { alert(this.responseText); };
		}
		window.location.reload();
		return;
	  }
	};
	xmlhttp.open("GET","sqlitemupdateaccount.php?itemparentid="+itemparentid+"&itemaccountto="+itemaccountto,true);
	xmlhttp.send();
}
function sqlaccounttogglefav(accountid,state) {
	
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
	  if (this.readyState == 4 && this.status == 200) {
		if(this.responseText) {
			if (this.responseText != 'Success') { alert(this.responseText); };
		}
		window.location.reload();
		return;
	  }
	};
	xmlhttp.open("GET","sqlaccounttogglefav.php?accountid="+accountid+"&state="+state,true);
	xmlhttp.send();
}
function updatefield(elementid,table,id,setfield,value,idfield) {
	  if (elementid == "") {
		return;
	  } else {
		var xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function() {
		  if (this.readyState == 4 && this.status == 200) {
			if(this.responseText) {
				if (this.responseText.trim() !== 'Success') { alert(this.responseText); }
				document.getElementById(elementid).value = this.responseText;
			}
			//window.location.reload();
			return;
		  }
		};
		document.getElementById(elementid).setAttribute("disabled","disabled");
		xmlhttp.open("GET","update.php?table="+table+"&id="+id+"&setfield="+setfield+"&value="+value+"&idfield="+idfield,true);
		xmlhttp.send();
		document.getElementById(elementid).removeAttribute("disabled");
		document.getElementById(elementid).value = value+' - Saved!';
		let timeout;
		function alertFunc() {
		  document.getElementById(elementid).value = value; //'$'+newvalue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		}
		timeout = setTimeout(alertFunc, 1000);
	  }
}
function sqlaccountinsert(type,name,balance) {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
	  if (this.readyState == 4 && this.status == 200) {
		if(this.responseText) {
			if (this.responseText != 'Success') { alert(this.responseText); };
		}
		window.location.reload();
		return;
	  }
	};
	xmlhttp.open("GET","sqlaccountinsert.php?type="+type+"&name="+name+"&balance="+balance,true);
	xmlhttp.send();
	window.location.reload();
}
$(document).ready(function () {

    $('#mytable').DataTable({
		searching: false,
		processing: true,
		sort: false,
		paging: false,
	});
	
	const idModal = document.getElementById('itemdeleteModal');

	idModal.addEventListener('show.bs.modal', event => {
	  // Button that triggered the modal
	  const button = event.relatedTarget;
	  // Extract info from data-bs-* attributes
	  const myitemid = button.getAttribute('data-bs-itemid');
	  const mydate = button.getAttribute('data-bs-date');
	  // Update the modal's content.
	  const modalTitle = idModal.querySelector('.modal-title');
	  modalTitle.textContent = `Delete Transaction #${myitemid}?`;
	  document.getElementById('modalitemid').value = myitemid;
	  
	  document.getElementById('delforwardtext').innerHTML = "Delete from this date ("+mydate+") forward";
	})
	const editModal = document.getElementById('itemeditModal');

	editModal.addEventListener('show.bs.modal', event => {
	  // Button that triggered the modal
	  const button = event.relatedTarget;
	  // Extract info from data-bs-* attributes
	  const itemid = button.getAttribute('data-bs-itemid');
	  const itemparentname = button.getAttribute('data-bs-itemparentname');
	  const itemparentamount = button.getAttribute('data-bs-itemparentamount');
	  const itemaccountfrom = button.getAttribute('data-bs-itemaccountfrom');
	  const itemaccountto = button.getAttribute('data-bs-itemaccountto');
	  const itemparentdate = button.getAttribute('data-bs-itemparentdate');
	  const itemparentrepeattype = button.getAttribute('data-bs-itemparentrepeattype');
	  const itemparentrepeatfreq = button.getAttribute('data-bs-itemparentrepeatfreq');
	  const itemparentrepeatdate = button.getAttribute('data-bs-itemparentrepeatdate');
	  // Update the modal's content.
	  document.getElementById('emodalitemid').value = itemid;
	  document.getElementById('eitemparentname').value = itemparentname;
	  document.getElementById('eitemparentamount').value = itemparentamount;
	  document.getElementById('eitemaccountfrom').value = itemaccountfrom;
	  document.getElementById('eitemaccountto').value = itemaccountto;
	  document.getElementById('eitemparentdate').value = itemparentdate;
	  document.getElementById('eitemparentrepeattype').value = itemparentrepeattype;
	  switchrecuredit(itemparentrepeattype.toString());
	  document.getElementById('eitemparentrepeatfreq').value = itemparentrepeatfreq;
	  document.getElementById('eitemparentrepeatdate').value = itemparentrepeatdate;
	})
	
});
</script>
 </head>
 <body>
  <div class="container-fluid" style="max-width: 95%">
   <div class="card border-0">
   <br />
   <h1 align="left"><a class="text-reset" href=home.php>My Data</a>
  <?php
	if(isset($_SESSION['user_image'])) {
  ?>
		<div class="dropdown float-end">
		  <button class="btn btn-light btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
			<img width=20 src="<?=$_SESSION['user_image']?>" class="rounded" />
		  </button>
		  <ul class="dropdown-menu">
			<li><a class="dropdown-item" href="logout.php">Logout</a></li>
		  </ul>
		</div> 
	<?php
	}
	?>
   </h1></div></div>
  <div class="container-fluid" style="max-width: 95%">
 
$(document).ready(function () {
  fecha = new Date();
  let DIA = 24 * 60 * 60 * 1000;
  nextday = new Date(fecha.getTime() + DIA);
  Year = fecha.getFullYear();
  day = fecha.getDate();
  month = fecha.getMonth() + 1;
  string1 = get_datesrt(Year, month, day);
  string2 = get_datesrt(nextday.getFullYear(), nextday.getMonth() + 1, nextday.getDate());
  y = 0;
  bandera1 = true;
  var flag = false;
  document.getElementById("card-x").style.visibility = "hidden";
  document.getElementById("header").style.visibility = "hidden"
  document.getElementById("max").style.visibility = "hidden"
  $('#select').click(function () {
    get_events();
  });

  $('#mes').html(str_month(month));
  $('#dia').html(day);
  setTimeout(getTime(), 1000);
  $("#max").click(function () {
    launchIntoFullscreen(document.getElementById("main_container"));
  });
  $("#conf").click(function () {
    if (document.getElementById("header").style.visibility == "hidden" && document.getElementById("max").style.visibility == "hidden") {
      document.getElementById("header").style.visibility = "visible"
      document.getElementById("max").style.visibility = "visible"
      flag = true
    } else {
      document.getElementById("header").style.visibility = "hidden"
      document.getElementById("max").style.visibility = "hidden"
      flag = false
    }
    if (flag) {
      setTimeout(function () {
        document.getElementById("header").style.visibility = "hidden"
        document.getElementById("max").style.visibility = "hidden"
      }, 15000);
    }
  });

  reloading();
});
function process_data(data) {
  list = document.getElementById("meet")
  if (list != 'token') {
    data = JSON.parse(data)
    list.innerHTML = ''
    globalThis.bandera = true;
    globalThis.time1 = []
    for (let i in data) {
      time = data[i]['start'].split('T')[1].split('.')[0] + '-' + data[i]['end'].split('T')[1].split('.')[0]
      globalThis.time1.push(time);
      list.innerHTML += `  <div class="card mb-3" id="card-${i}">
          <div class="card-body">
            <h5 class="card-title" id="card-title-${i}">${data[i]['subject']}</h5>
            <div class="card-text" id="card-text-${i}">
             ${time}
              <h6 id="name-${i}"> ${data[i]['organizer']['name']} </h6>
            </div>
          </div>
        </div>`


    }
  }
}
function get_datesrt(Y, m, d) {
  if (m.toString().length == 1 && d.toString().length == 1) {
    string = Y + "-0" + m + "-0" + d;
  } 
  if(m.toString().length == 1 && d.toString().length != 1){
    string = Y + "-0" + m + "-" + d;
  }
  if(m.toString().length != 1 && d.toString().length == 1){
    string = Y + "-" + m + "-0" + d;
  }
  if(m.toString().length != 1 && d.toString().length != 1){
    string = Y + "-" + m + "-" + d;
  }
  return string
}
function get_events() {
  fecha = new Date();
  let DIA = 24 * 60 * 60 * 1000;
  nextday = new Date(fecha.getTime() + DIA);
  Year = fecha.getFullYear();
  day = fecha.getDate();
  month = fecha.getMonth() + 1;
  $('#mes').html(str_month(month));
  $('#dia').html(day);
  string1 = get_datesrt(Year, month, day);
  string2 = get_datesrt(nextday.getFullYear(), nextday.getMonth() + 1, nextday.getDate());
  x = document.getElementById('select').selectedIndex;
  if (x != 0 && x != y) {
    id = document.getElementById('select').options[x].value;
    var data = {
      data: string1,
      next: string2, calendar: id
    };
    $.get('../events.php', data, process_data)
    y = x
  }
}
function launchIntoFullscreen(element) {
  if (!document.fullscreenElement) {
    if (element.requestFullscreen) {
      element.requestFullscreen();
    } else if (element.mozRequestFullScreen) {
      element.mozRequestFullScreen();
    } else if (element.webkitRequestFullscreen) {
      element.webkitRequestFullscreen();
    } else if (element.msRequestFullscreen) {
      element.msRequestFullscreen();
    }

  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
      document.webkitExitFullscreen();
    }

  }


}

function reloading() {
  if (typeof (timeOutReload) == "undefined" || timeOutReload == 0) {
    timerId = setTimeout(function () {
      y = 0;
      get_events();
      setTimeout("reloading()", 1000);
    }, 240000);
    timeOutReload = 0;
  }
}

function state(hora, minuto, segundo) {

  if (globalThis.time1 != undefined && globalThis.time1 != '') {
    time = globalThis.time1
    globalThis.bandera = true;
    for (var i = 0; i < time.length; i++) {
      limits = time[i].split('-');
      date = new Date("0", "0", "0", hora, minuto, segundo);
      limit1 = limits[0].split(":")
      limit1 = new Date("0", "0", "0", limit1[0], limit1[1], limit1[2]);
      limit2 = limits[1].split(":")
      limit2 = new Date("0", "0", "0", limit2[0], limit2[1], limit2[2]);

      if (limit1 <= date && date <= limit2) {
        document.getElementById("state").src = "../images/room-busy.png"
        old = document.getElementById("card-" + i).innerHTML
        document.getElementById("card-x").style.visibility = "visible";
        $("#card-x").css('display', 'block');
        document.getElementById("card-x").innerHTML = old;
        document.getElementById("card-x").style.background = '#dc5b3e';
        document.getElementById("card-x").className = "card mb-3 text-light";
        document.getElementById("card-" + i).style.visibility = "hidden";
        $("#card-" + i).css('display', 'none');
        if (bandera1) {
          $('html, body').animate({
            scrollTop: $("#card-x").offset().top - 200
          },
            500);
          bandera1 = false;
        }
        globalThis.bandera = false;
      }
    }
    if (globalThis.bandera) {
      document.getElementById("state").src = "../images/room-free.png"
      document.getElementById("card-x").style.visibility = "hidden";
      $("#card-x").css('display', 'none');


      for (var i = 0; i < time.length; i++) {

        document.getElementById("card-" + i).style.visibility = "visible";
        $("#card-" + i).css('display', 'block');
      }



    }


  }
  if (globalThis.bandera) {
    document.getElementById("state").src = "../images/room-free.png"
    document.getElementById("card-x").style.visibility = "hidden";
    $("#card-x").css('display', 'none');
  }
}

function str_month(mes) {
  let Meses = ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Ago", "Sep", "Nov", "Dic"];
  return Meses[mes - 1];
}

function getTime() {
  var fecha_js = new Date();
  var hora = fecha_js.getHours();
  hora = hora < 10 ? "0" + hora : hora;
  var minuto = fecha_js.getMinutes();
  minuto = minuto < 10 ? "0" + minuto : minuto;
  var segundo = fecha_js.getSeconds();
  segundo = segundo < 10 ? "0" + segundo : segundo;
  state(hora, minuto, segundo);
  $("#clock").html(hora + ":" + minuto);

  setTimeout("getTime();", 1000);

}


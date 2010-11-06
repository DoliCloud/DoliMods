
var ie = (document.all) ? true : false;

if (!ie) document.captureEvents(Event.MOUSEMOVE);

document.Preview.onclick = getclickPosXY
document.Preview.onmousemove = getPosXY;

var element_lx = 0;
var element_ty = 0;

var x = 0;
var y = 0;

var facktor = parseFloat(document.menueForm.preview_scale.value);
var preview_width  = parseInt(document.menueForm.preview_width.value);
var preview_height = parseInt(document.menueForm.preview_height.value);
var preview_border = parseInt(document.menueForm.preview_border.value);


// ---------------------------------------------------------------------

function get_html_element_xy(e)
{
  var left = 0;
  var top =  0;

  if (!e) { e = window.event; }

  var myTarget = e.currentTarget;
  if (!myTarget)
  {
    myTarget = e.srcElement;
  }
  else
  {
    if (myTarget == "undefined")
    {
      myTarget = e.srcElement;
    }
  }

  while (myTarget != document.body)
  {
    left += myTarget.offsetLeft;
    top += myTarget.offsetTop;
    myTarget = myTarget.offsetParent;
  }

  element_lx = left;
  element_ty = top;

  //alert("left: " + left + "\ntop: " + top);
}


// ---------------------------------------------------------------------

function get_preview_image_xy(e)
{
  get_html_element_xy(e);

  x = (ie) ? event.clientX + document.body.scrollLeft : e.pageX;
  y = (ie) ? event.clientY + document.body.scrollTop  : e.pageY;

  x = x - element_lx - preview_border - 1;
  y = y - element_ty - preview_border - 2;

  x = Math.round(x * facktor);
  y = Math.round(y * facktor);

  if (x < 0) { x = 0; }
  if (x > preview_width) { x = preview_width; }
  if (y < 0) { y = 0; }
  if (y > preview_height) { y = preview_height; }
}


// ---------------------------------------------------------------------

function setPageSize(form)
{
  var page_size = form.pagesize[form.pagesize.selectedIndex].value.split(",");
  var page_x = parseInt(page_size[0]);
  var page_y = parseInt(page_size[1]);

  if ((page_x > 0) && (page_y > 0))
  {
    setGeometry(0, 0, page_x, page_y);
  }

  //document.menueForm.debug.value = form.pagesize[form.pagesize.selectedIndex].value;

  return(true);
}


// ---------------------------------------------------------------------

function getclickPosXY(e)
{
  get_preview_image_xy(e);

  setPreview(x,y);

  return(true);
}


// ---------------------------------------------------------------------

function getPosXY(e)
{
  get_preview_image_xy(e);

  document.menueForm.PosX.value = x;
  document.menueForm.PosY.value = y;

  return(true);
}


// ---------------------------------------------------------------------

function setPreview(x,y)
{
  var oldL = parseInt(document.menueForm.geometry_l.value);
  var oldT = parseInt(document.menueForm.geometry_t.value);
  var oldX = oldL + parseInt(document.menueForm.geometry_x.value);
  var oldY = oldT + parseInt(document.menueForm.geometry_y.value);
  var newL = oldL;
  var newT = oldT;
  var newX = oldL;
  var newY = oldT;

  if(document.menueForm.ecke[1].checked)
  {
    // setting bottom right
    newX = x - oldL;
    newY = y - oldT;

    if (newX < 0) newX = 0;
    if (newY < 0) newY = 0;

    document.menueForm.ecke[0].checked = true;
    document.getElementById("ecke_rot1").style.color = "red";
    document.getElementById("ecke_rot2").style.color = "black";
  }
  else
  {
    // setting top left
    newL = x;
    newT = y;
    newX = oldX - newL;
    newY = oldY - newT;

    if (newX < 0) newX = 0;
    if (newY < 0) newY = 0;

    document.menueForm.ecke[1].checked = true;
    document.getElementById("ecke_rot1").style.color = "black";
    document.getElementById("ecke_rot2").style.color = "red";
  }

  setGeometry(newL, newT, newX, newY);

  return(true);
}


// ---------------------------------------------------------------------

function setGeometry(l, t, x, y)
{
  document.menueForm.geometry_l.value = l;
  document.menueForm.geometry_t.value = t;
  document.menueForm.geometry_x.value = x;
  document.menueForm.geometry_y.value = y;
}

// ---------------------------------------------------------------------

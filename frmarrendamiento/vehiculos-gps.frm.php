<?php
/**
 * @author Balam Gonzalez Luis Humberto
 * @version 0.0.01
 * @package
 */
//=====================================================================================================
	include_once("../core/go.login.inc.php");
	include_once("../core/core.error.inc.php");
	include_once("../core/core.html.inc.php");
	include_once("../core/core.init.inc.php");
	include_once("../core/core.db.inc.php");
	$theFile			= __FILE__;
	$permiso			= getSIPAKALPermissions($theFile);
	if($permiso === false){	header ("location:../404.php?i=999");	}
	$_SESSION["current_file"]	= addslashes( $theFile );
//=====================================================================================================
$xHP		= new cHPage("TR.PAQUETESGPS", HP_FORM);
$xQL		= new MQL();
$xLi		= new cSQLListas();
$xF			= new cFecha();
$xDic		= new cHDicccionarioDeTablas();
$xUser			= new cSystemUser(getUsuarioActual()); $xUser->init();
$xRuls			= new cReglaDeNegocio();
$originador		= 0;
$suborigen		= 0;
$EsAdmin		= false;
$NoUsarUsers	= $xRuls->getArrayPorRegla($xRuls->reglas()->CREDITOS_ARREND_NOUSERS);
$EsOriginador	= false;

if($xUser->getEsOriginador() == true){
	$xOrg	= new cLeasingUsuarios();
	if($xOrg->initByIDUsuario($xUser->getID()) == true){
		$originador	= $xOrg->getOriginador();
		$suborigen	= $xOrg->getSubOriginador();
		//$EsActivo	= $xOrg->getEsActivo();
		//$EsAdmin	= $xOrg->getEsAdmin();
		if($xOrg->getEsAdmin() == true){
			$suborigen			= 0;
		}
		if($xOrg->getEsActivo() == false){
			$xHP->goToPageError(403);
		} else {
			$EsOriginador	= true;
		}
	}
}
//$jxc 		= new TinyAjax();
//$jxc ->exportFunction('datos_del_pago', array('idsolicitud', 'idparcialidad'), "#iddatos_pago");
//$jxc ->process();
$clave		= parametro("id", 0, MQL_INT); $clave		= parametro("clave", $clave, MQL_INT);  
$fecha		= parametro("idfecha-0", false, MQL_DATE); $fecha = parametro("idfechaactual", $fecha, MQL_DATE);  $fecha = parametro("idfecha", $fecha, MQL_DATE);
$persona	= parametro("persona", DEFAULT_SOCIO, MQL_INT); $persona = parametro("socio", $persona, MQL_INT); $persona = parametro("idsocio", $persona, MQL_INT);
$credito	= parametro("credito", DEFAULT_CREDITO, MQL_INT); $credito = parametro("idsolicitud", $credito, MQL_INT); $credito = parametro("solicitud", $credito, MQL_INT);
$cuenta		= parametro("cuenta", DEFAULT_CUENTA_CORRIENTE, MQL_INT); $cuenta = parametro("idcuenta", $cuenta, MQL_INT);
$jscallback	= parametro("callback"); $tiny = parametro("tiny"); $form = parametro("form"); $action = parametro("action", SYS_NINGUNO);
$monto		= parametro("monto",0, MQL_FLOAT); $monto	= parametro("idmonto",$monto, MQL_FLOAT); 
$recibo		= parametro("recibo", 0, MQL_INT); $recibo	= parametro("idrecibo", $recibo, MQL_INT);
$empresa	= parametro("empresa", 0, MQL_INT); $empresa	= parametro("idempresa", $empresa, MQL_INT); $empresa	= parametro("iddependencia", $empresa, MQL_INT); $empresa	= parametro("dependencia", $empresa, MQL_INT);
$grupo		= parametro("idgrupo", 0, MQL_INT); $grupo	= parametro("grupo", $grupo, MQL_INT);
$ctabancaria = parametro("idcodigodecuenta", 0, MQL_INT); $ctabancaria = parametro("cuentabancaria", $ctabancaria, MQL_INT);

$observaciones= parametro("idobservaciones");
$xHP->addJTableSupport();
$xHP->init();

$xFRM	= new cHForm("frmvehiculos_gps", "vehiculos-gps.frm.php?action=$action");
$xSel		= new cHSelect();
$xFRM->setTitle($xHP->getTitle());
$xFRM->addCerrar();

/* ===========		GRID JS		============*/

$xHG	= new cHGrid("iddivgps",$xHP->getTitle());
$xHG->setOrdenar();

$xHG->setSQL("SELECT * FROM `vehiculos_gps` LIMIT 0,100");
$xHG->addList();
$xHG->addKey("idvehiculos_gps");
$xHG->col("nombre_gps", "TR.NOMBRE GPS", "10%");
if($EsOriginador == false){
	$xHG->OToolbar("TR.AGREGAR", "jsAdd()", "add.png");
	$xHG->OButton("TR.EDITAR", "jsEdit('+ data.record.idvehiculos_gps +')", "edit.png");
	$xHG->OButton("TR.ELIMINAR", "jsDel('+ data.record.idvehiculos_gps +')", "delete.png");
}
$xFRM->addHElem("<div id='iddivgps'></div>");
$xFRM->addJsCode( $xHG->getJs(true) );
echo $xFRM->get();
?>
<script>
var xG	= new Gen();
function jsEdit(id){
	xG.w({url:"../frmarrendamiento/vehiculos-gps.edit.frm.php?clave=" + id, tiny:true, callback: jsLGiddivgps});
}
function jsAdd(){
	xG.w({url:"../frmarrendamiento/vehiculos-gps.new.frm.php?", tiny:true, callback: jsLGiddivgps});
}
function jsDel(id){
	xG.rmRecord({tabla:"vehiculos_gps", id:id, callback:jsLGiddivgps});
}
</script>
<?php
	
//$jxc ->drawJavaScript(false, true);
$xHP->fin();
?>
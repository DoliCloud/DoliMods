<?php
// Load Dolibarr environment
$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include str_replace("..", "", $_SERVER["CONTEXT_DOCUMENT_ROOT"])."/main.inc.php";
// Try main.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res && file_exists("../../../../main.inc.php")) $res=@include "../../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT."/core/lib/admin.lib.php";
require_once DOL_DOCUMENT_ROOT."/core/lib/files.lib.php";
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formadmin.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/class/html.formfile.class.php';


/*
 * View
 */

$form=new Form($db);
$formfile=new FormFile($db);

$arrayofcss=array('/facturxutil/styles.css');

llxHeader('', 'FacturX', '', '', 0, 0, '', $arrayofcss);

mkdir($conf->facturxutil->dir_temp);

include_once './includes/autoloader.php';
include_once DOL_DOCUMENT_ROOT.'/includes/tecnickcom/tcpdf/tcpdf_parser.php';
include_once './includes/setasign/fpdf/fpdf.php';


			$resultHeaderClass = 'warning';
			$resultHeaderHtml = '';
			$resultBodyHtml = '';
if ($_FILES['pdf_facturx_extract']) {
	$resultHeaderHtml = 'Extract Factur-X XML from PDF result';
	$facturx = new \Atgp\FacturX\Facturx();
	$resultBodyHtml .= "<h4 class='text-primary'>File ".$_FILES['pdf_facturx_extract']['name'].' : </h4>';
	try {
		$result = $facturx->getFacturxXmlFromPdf($_FILES['pdf_facturx_extract']['tmp_name'], true);
	} catch (Exception $e) {
		$resultBodyHtml .= '<pre>Error while retrieving XML Factur-X :'.$e.'</pre>';
	}

	if (!$result) {
		$resultBodyHtml .= '<div class="alert alert-danger">No valid XML Factur-X found (getFacturxXmlFromPdf return false).</div>';
	} else {
		$resultHeaderClass = 'success';
		$doc = new DomDocument('1.0');
		$doc->preserveWhiteSpace = false;
		$doc->formatOutput = true;
		$doc->loadXML($result);
		$resultBodyHtml .= '<textarea lang="xml" class="centpercent" style="height: 300px">'.htmlentities($doc->saveXML()).'</textarea>';
	}
}
if ($_FILES['xml_facturx_check']) {
	$facturx = new \Atgp\FacturX\Facturx();
	$resultHeaderHtml = 'Check XML Factur-X result';
	$resultBodyHtml = "<h4 class='text-primary'>File ".$_FILES['xml_facturx_check']['name'].' : </h4>';
	try {
		$result = $facturx->checkFacturxXsd($_FILES['xml_facturx_check']['tmp_name']);
	} catch (Exception $e) {
		$resultBodyHtml .= '<pre>Error while checking the XML :'.$e.'</pre>';
	}
	if ($result === true) {
		$resultHeaderClass = 'success';
		$resultBodyHtml .= '<div class="alert alert-success">XML Factur-X valid.</div>';
	} else {
		$resultBodyHtml .= '<div class="alert alert-warning">XML Factur-X invalid.</div>';
	}
}
if ($_FILES['pdf_classic'] && $_FILES['xml_facturx_tolink']) {
	$facturx = new \Atgp\FacturX\Facturx();
	$resultHeaderHtml = 'Generate PDF Factur-X from PDF and Factur-X XML result';
	try {
		if ($_POST['file_as_string'] == 'true') {
			$pdf = file_get_contents($_FILES['pdf_classic']['tmp_name']);
			$facturx_xml = file_get_contents($_FILES['xml_facturx_tolink']['tmp_name']);
		} else {
			$pdf = $_FILES['pdf_classic']['tmp_name'];
			$facturx_xml = $_FILES['xml_facturx_tolink']['tmp_name'];
		}
		$attachment_files = array();
		if (!empty($_FILES['attachment']['tmp_name'])) {
			$attachment_files[] = array(
				'name' => $_FILES['attachment']['name'],
				'desc' => $_POST['attachment_desc'],
				'path' => $_FILES['attachment']['tmp_name'],
			);
		}
		$result = $facturx->generateFacturxFromFiles($pdf, $facturx_xml,
			'autodetect', true, $conf->facturxutil->dir_temp.'/', $attachment_files, true);
	} catch (Exception $e) {
		$resultBodyHtml = 'Error while generating the Factur-X :<pre>' . $e.'</pre>';
	}
	if (!empty($result)) {
		$resultHeaderClass = 'success';
		$resultBodyHtml = '<div class="alert alert-success">Factur-X PDF file '.$conf->facturx->dir_temp.'/ successfully generated.</div>';
	} else {
		$resultBodyHtml = '<div class="alert alert-warning">Impossible to generate the Factur-X PDF file.</div>'.$resultBodyHtml;
	}
}
?>
		<div class="container-fluid">
			<div class="card">
				<div class="card-header bg-info text-white">
					<h2>Factur-X Utilities</h2>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-4">
							<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="token" value="<?php echo newToken(); ?>">
								<div class="card">
									<div class="card-header bg-info text-white">
										Generate a PDF Factur-X from a PDF and Factur-X XML
									</div>
									<div class="card-body">
										<div class="form-group">
											<label>Choose the PDF file</label>
											<input type="file" class="form-control-file" name="pdf_classic" required>
										</div>
										<div class="form-group">
											<label>Choose the Factur-X XML file to link</label>
											<input type="file" class="form-control-file" name="xml_facturx_tolink" required>
										</div>
										<div class="form-group">
											<label>(Optional) Choose a file to link :</label>
											<input type="file" class="form-control-file" name="attachment">
										</div>
										<div class="form-group">
											<label for="attachment_desc">Description of the attachment :</label>
											<input type="text" id="attachment_desc" class="form-control" name="attachment_desc">
										</div>
									</div>
									<div class="card-footer text-center">
										<input class="btn btn-primary button" type="submit" value="Submit">
									</div>
								</div>
							</form>
						</div>
						<div class="col-md-4">
							<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="token" value="<?php echo newToken(); ?>">
								<div class="card">
									<div class="card-header bg-info text-white">Extract Factur-X XML from a PDF</div>
									<div class="card-body">
										<div class="form-group">
											<label>Choose the PDF file containing the Factur-X XML to extract :</label>
											<input type="file" class="form-control-file" name="pdf_facturx_extract" required>
										</div>
									</div>
									<div class="card-footer text-center">
										<input class="btn btn-primary button" type="submit" value="Submit">
									</div>
								</div>
							</form>
						</div>
						<div class="col-md-4">
							<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="token" value="<?php echo newToken(); ?>">
								<div class="card">
									<div class="card-header bg-info text-white">Verify Factur-X XML file</div>
									<div class="card-body">
										<div class="form-group">
											<label>Choose the Factur-X XML file to check :</label>
											<input type="file" class="form-control-file" name="xml_facturx_check" required>
										</div>
									</div>
									<div class="card-footer text-center">
										<input class="btn btn-primary button" type="submit" value="Submit">
									</div>
								</div>
							</form>
						</div>
					</div>
					<?php if (!empty($resultBodyHtml)) { ?>
						<div class="row">
							<div class="col-md-12">
								<div class="card">
									<div class="card-header bg-<?php echo $resultHeaderClass ?> text-white">
										<?php echo $resultHeaderHtml ?>
									</div>
									<div class="card-body">
										<?php echo $resultBodyHtml ?>
									</div>
								</div>
							</div>
						</div>
						<br/>
					<?php } ?>
				</div>
			</div>
		</div>
<?php
llxFooter();
$db->close();

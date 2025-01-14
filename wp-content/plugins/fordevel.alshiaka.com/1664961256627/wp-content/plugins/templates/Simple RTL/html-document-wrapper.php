<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title><?php echo $this->get_title(); ?></title>
	<style type="text/css"><?php $this->template_styles(); ?></style>
	<style type="text/css"><?php do_action( 'wpo_wcpdf_custom_styles', $this->get_type(), $this ); ?></style>
	<style>
	    tr#tr-company-info .right, tr#tr-company-info .right * {
	        float: right;
	        direction: rtl;
	        text-align: right;
	        display: inline;
	    }
	    tr#tr-company-info .left, tr#tr-company-info .left * {
	        float: left;
	        direction: ltr;
	        text-align: left;
	        display: inline;
	    }
	</style>
</head>
<body class="<?php echo $this->get_type(); ?>">
<?php echo $content; ?>
</body>
</html>
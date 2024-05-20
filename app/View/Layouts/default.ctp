<?php

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

?>
<!DOCTYPE html>
<html>

<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $this->fetch('title'); ?>
	</title>
	<?php
	echo $this->Html->meta('icon');

	echo $this->Html->css(['bootstrap.min.css', 'jquery-ui.min.css', 'select2.min.css', 'select2-bootstrap.min.css']);
	echo $this->Html->script(["jquery.min.js", 'jquery-ui.min.js', 'select2.min.js']);

	echo $this->fetch('meta');
	echo $this->fetch('css');
	echo $this->fetch('script');
	?>
</head>

<body>

	<div class="wrapper d-flex justify-content-center">
		<div class="col-10">
			<?php if (isset($_SESSION["Auth"]["User"]) && !empty($_SESSION["Auth"]["User"])) : ?>

				<div class="navbar navbar-expand-lg">
					<span class="navbar-brand">Message Board</span>
					<ul class="navbar-nav ml-auto">
						<li class="nav-item">
							<?= $this->Html->link(
								"Messages",
								array(
									"controller" => "messages",
									"action" => "index"
								),
								array(
									"class" => "nav-link"
								)
							); ?>
						</li>
						<li class="nav-item">
							<?= $this->Html->link(
								"Profile",
								array(
									"controller" => "users",
									"action" => "profile"
								),
								array(
									"class" => "nav-link"
								)
							); ?>
						</li>
						<li class="nav-item">
							<?= $this->Html->link(
								"Logout",
								array(
									"controller" => "users",
									"action" => "logout"
								),
								array(
									"class" => "nav-link text-danger"
								)
							); ?>
						</li>
					</ul>
				</div>

			<?php endif; ?>

			<?php echo $this->fetch('content'); ?>
			<?php //echo $this->element('sql_dump');
			?>
		</div>
	</div>
</body>

</html>
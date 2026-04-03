<?php

$title_page = f_translate('Information');
f_page_title_set($title_page);

$links = [
	['href' => f_page_link('page_about_us'), 'label' => f_translate('About us')],
	['href' => f_page_link('page_rules'), 'label' => f_translate('Terms of service')],
	['href' => f_page_link('page_privacy'), 'label' => f_translate('Privacy policy')],
	['href' => f_page_link('page_faq'), 'label' => f_translate('FAQ')],
	['href' => f_page_link('page_safety_tips'), 'label' => f_translate('Safety tips')],
	['href' => f_page_link('page_payment'), 'label' => f_translate('Payment')],
	['href' => f_page_link('page_recomendations'), 'label' => f_translate('Recommendations')],
];

?>

<div class="container py-4">
	<h1 class="h3 mb-4"><?php f_translate_echo('Information'); ?></h1>
	<ul class="list-group list-group-flush shadow-sm rounded border">
		<?php foreach( $links as $row ){ ?>
			<li class="list-group-item">
				<a href="<?php f_echo_html($row['href']); ?>" class="text-decoration-none"><?php f_echo_html($row['label']); ?></a>
			</li>
		<?php } ?>
	</ul>
</div>

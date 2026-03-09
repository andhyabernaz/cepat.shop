<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<?php foreach ($categories as $category) { ?>
		<url>
			<loc><?php echo route('product.bycategory', $category->slug); ?></loc>
			<?php if (isset($category->updated_at)) { ?>
				<lastmod><?php echo $category->updated_at; ?></lastmod>
			<?php } ?>
			<changefreq>monthly</changefreq>
			<priority>0.8</priority>
		</url>
	<?php } ?>
	<?php foreach ($products as $product) { ?>
		<url>
			<loc><?= route('product.show', $product->slug); ?></loc>
			<lastmod><?php echo $product->updated_at; ?></lastmod>
			<changefreq>weekly</changefreq>
			<priority>0.8</priority>
		</url>
	<?php } ?>
	<?php foreach ($posts as $post) { ?>
		<url>
			<loc><?= route('post.show', $post->slug); ?></loc>
			<lastmod><?php echo $post->updated_at; ?></lastmod>
			<changefreq>weekly</changefreq>
			<priority>0.8</priority>
		</url>
	<?php } ?>
</urlset>
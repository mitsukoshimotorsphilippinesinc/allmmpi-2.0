<?php 
echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
?>
<rss version="2.0"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
    xmlns:admin="http://webns.net/mvcb/"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:content="http://purl.org/rss/1.0/modules/content/">

    <channel>
    
    <title><?php echo $feed_name; ?></title>

    <link><?php echo $feed_url; ?></link>
    <description><?php echo $page_description; ?></description>
    <dc:language><?php echo $page_language; ?></dc:language>

    <dc:rights>Copyright <?php echo gmdate("Y", time()); ?></dc:rights>

    <?php foreach($posts as $entry): ?>
    
        <item>

          <title><?php echo xml_convert($entry->title); ?></title>
          <link><?php echo site_url('main/news/view/' . $entry->news_id) ?></link>

          <description><![CDATA[
      <?= $entry->short_body ?>
      ]]></description>
          <pubDate><?php echo $entry->insert_timestamp;?></pubDate>
        </item>

        
    <?php endforeach; ?>
    
    </channel></rss> 
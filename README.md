# wp-meta-filter
Filter data by metadata in wordpress admin post/data list page

## usage

````php
$deals = new Wenprise\MetaFilter\PostFilter();

$deals->set_post_type('client')
      ->set_meta_key('_deal_price')
      ->set_query_var('deal')
      ->set_header('成交金额大于')
      ->set_compare('>')
      ->set_options([
          0     => '0（已成交）',
          10000 => '10000 元',
          20000 => '20000 元',
      ]);
````

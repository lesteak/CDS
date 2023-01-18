<?php
function get_group_data_murmarations($post) {
  $logo = get_field('logo', $post);
  $logo = ($logo && $logo['type'] === 'image') ? $logo['sizes']['large'] : '';

  $data = array(
    'id' => $post->ID,
    'title' => $post->post_title,
    'url' => get_the_permalink($post),
    'logo' => $logo,

    'hubs' => endpoint_get_taxonomy_terms($post, 'hub'),
    'countries' => endpoint_get_taxonomy_terms($post, 'country'),
    'topics' => endpoint_get_taxonomy_terms($post, 'topic'),

    'description' => get_field('description', $post),
    
    'location' => endpoint_get_location($post, null),
    'contact' => endpoint_get_contact($post),
    'last_updated' => get_the_modified_date('Y-m-d H:i:s', $post)
  );
  
  return $data;
}

function endpoint_get_groups_murmarations(WP_REST_Request $request) {
  $data = [];

  $args = array(
    'post_type' => 'initiatives',
    'posts_per_page' => -1
  );
  
  $post_query = new WP_Query($args);

  if($post_query->have_posts()) {
    $i = 0;
    while($post_query->have_posts()) : $post_query->the_post();
      global $post;

      $map = get_field('map');
      $logo = get_field('logo');

      $link_fields = array('twitter', 'facebook', 'instagram', 'youtube');
      $links = array();
      foreach($link_fields as $link_field) {
        if(get_field($link_field)) {
          $links[] = array(
            'name' => ucwords($link_field),
            'url' => get_field($link_field)
          );
        }
      }

      $additional = get_field('additional_web_addresses');
      if($additional) {
        foreach($additional as $item) {
          $links[] = array(
            'name' => $item['label'],
            'url' => $item['address']
          );
        }
      }
      
      if(get_field('email')) {
        $links[] = array(
          'name' => 'email',
          'url' => 'mailto:' . get_field('email')
        );
      }
    
      $data[$i]['linked_schemas'] = array('organizations_schema-v1.0.0');
      $data[$i]['name'] = html_entity_decode(get_the_title());
      $data[$i]['primary_url'] = (get_field('website')) ? get_field('website') : get_the_permalink();
      $data[$i]['urls'] = $links;
      $data[$i]['description'] = get_field('description');
      $data[$i]['locality'] = get_field('city');
      $data[$i]['region'] = get_field('province');
      $data[$i]['country_name'] = endpoint_get_taxonomy_terms($post, 'country');
      $data[$i]['geolocation'] = array(
        'lat' => $map['markers'][0]['lat'],
        'lon' => $map['markers'][0]['lng'],
      );
      $data[$i]['image'] = ($logo && $logo['type'] === 'image') ? $logo['sizes']['large'] : '';

      $tags = array('Transition Group');
      $topics = get_the_terms($post, 'topic');
      if($topics) {
        foreach($topics as $term) {
          $tags[] = html_entity_decode($term->name);
        }
      };

      $data[$i]['tags'] = $tags;

      $data[$i]['metadata'] = array(
        'sources' => array(
          'name' => 'Transition Groups',
          'profile_data_url' => get_the_permalink()
        )
      );

      $i ++;

    endwhile;
  }


  if(!empty($data)) {
    return $data;
  } else {
    return array(
      'body' => 'No Records Found'
    );
  }
}

<?php
function ajax_get_post_markers($params, $cache_expiry) {
  if(in_array($params['type'], array('1', '2', '4'))) {
    
    if($params['type'] === '1') {
      $args['post_type'] = array('initiatives', 'trainers');
    }
    
    if($params['type'] === '2') {
      $args['post_type'] = array('initiatives');
    }
    
    if($params['type'] === '4') {
      $args['post_type'] = array('trainers');
    }
    
    $args['posts_per_page'] = -1;
    
    if(array_key_exists('hub_name', $params)) {
      $args['tax_query'] = array(
        array (
          'taxonomy' => 'hub',
          'field' => 'slug',
          'terms' => $params['hub_name']
        )
      );
    } else if(array_key_exists('country', $params)) {
      $args['tax_query'] =  array(
        array (
          'taxonomy' => 'country',
          'field' => 'slug',
          'terms' => $params['country']
        )
      );
    }
  
    if(!empty($_POST['value']['search'])) {
      $args['s'] = $_POST['value']['search'];
    }

    $path = TEMPLATEPATH . '/cache/' . md5(serialize($args)) . '.json';

    if(!file_exists($path) || filemtime($path) < time() - $cache_expiry ) {
      $posts = get_posts($args);

      if($posts) {
        $results = array();
        foreach($posts as $post) {
          
          $map = get_field('map', $post->ID);

          $post_type = get_post_type($post->ID);
          
          //only show those that aren't saved with the default coords
          if($map['markers']) {
            $results[$post_type][$post->ID]['type'] = $post_type;
            $results[$post_type][$post->ID]['lat'] = $map['lat'];
            $results[$post_type][$post->ID]['lng'] = $map['lng'];
            $results[$post_type][$post->ID]['permalink'] = parse_post_link($post->ID);
            $results[$post_type][$post->ID]['title'] = get_the_title($post->ID);
            $results[$post_type][$post->ID]['age'] = get_initiatve_age($post->ID);
          } 
        }

        file_put_contents($path, json_encode($results));
        
        return $results;
      }

    } else {
      return json_decode(file_get_contents($path), true);
    }
  }

  return array();
}

function ajax_get_hub_markers($params, $cache_expiry) {
  //hub queries second
  if($params['type'] === "1" || $params['type'] === "3") {

    if(array_key_exists('hub_name', $params)) {
    
      $hub_term = get_term_by('slug', $params['hub_name'], 'hub');
      $hubs = ($hub_term) ? array($hub_term) : null;
    
    } else if(array_key_exists('country', $params)) {

      $country_term = get_term_by('slug', $params['country'], 'country');

      if($country_term) {
        $country_term_id = $country_term->term_id;
      
        $args = array(
          'hide_empty' => false,
          'meta_query' => array(
            array(
              'key' => 'associated_countries',
              'value' => '\;i\:' . $country_term_id . '\;|\"' . $country_term_id . '\";',
              'compare' => 'REGEXP'
            )
          )
        );
  
        $hubs = get_terms('hub', $args);
      }
    } else {
      $args = array(
        'hide_empty' => false
      );
      
      $hubs = get_terms('hub', $args);
    }

    if($hubs) {
      $results = array();
      
      foreach($hubs as $hub) {
        if(
          !array_key_exists('training', $params) ||
          (array_key_exists('training', $params) && get_field('training', $hub) === TRUE)
          ) {
            
          $map = get_field('map', $hub);
          
          $results[$hub->term_id]['type'] = 'hubs';
          $results[$hub->term_id]['lat'] = $map['lat'];
          $results[$hub->term_id]['lng'] = $map['lng'];
          $results[$hub->term_id]['permalink'] = get_term_link($hub);
          $results[$hub->term_id]['title'] = $hub->name;
          $results[$hub->term_id]['training'] = get_field('training', $hub);
        }
      }

      return $results;
    }
  }

  return array();
} 

function ajax_get_map_markers() {
  //create cache dir
  if (!file_exists(TEMPLATEPATH . '/cache')) {
    mkdir(TEMPLATEPATH . '/cache', 0755, true);
  }
  
  $cache_expiry = 24 * 60 * 60; //24hrs

  if($_POST === 'getMarkers') {
    return false;
  }

  $params = array_key_exists('params', $_POST['value']) ? $_POST['value']['params'] : array();
  
  if(!array_key_exists('type', $params)) {
    // ALL/BOTH = 1
    // INITIATIVES = 2
    // HUBS = 3
    // TRAINERS = 4
    
    $params['type'] = "1";
  }

  //handle legacy embed codes which use hub_id property instead of hub_name
  if(array_key_exists('hub_id', $params)) {
    $params['hub_name'] = get_term_by('id', $params['hub_id'], 'hub')->slug;
  }

  $post_markers = ajax_get_post_markers($params, $cache_expiry);
  
  $markers = array();
  $markers = array(
    'initiatives' => ($post_markers['initiatives']) ? $post_markers['initiatives'] : array(),
    // 'trainers' => ($post_markers['trainers']) ? $post_markers['trainers'] : array(),
    'hubs' => ajax_get_hub_markers($params, $cache_expiry)
  );
  
  echo json_encode($markers);
  wp_die();
}

add_action('wp_ajax_nopriv_getMarkers', 'ajax_get_map_markers');
add_action('wp_ajax_getMarkers', 'ajax_get_map_markers');
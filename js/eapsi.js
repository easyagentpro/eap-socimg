//console.log('wp option data');

var fi = (socData['featured_img'] == 1);
//console.log(fi);
var socImg = socData.soc_img;
var isHome = (socData.is_home == 1);
//console.log(isHome);
var fUrl = socData.featured_url;
var socTag =        document.createElement('meta');
socTag.setAttribute('property','og:image');

socTag.content  =  fi ? fUrl : socImg;
socTag.id  =        'eapvSocImg';
var testTag;
(function($){
//    $(document).ready(function(){
        testTag = $('head meta[property="og:image"]');
        var existTag = $('head meta[property="og:url"]');
//        console.log(testTag.length);
//        console.log(testTag);

//        if(testTag.length <= 0){
//            document.getElementsByTagName('head')[0].appendChild(socTag);
           if(existTag.length > 0){
               existTag.after(socTag);
           }else{
               $('head').append(socTag);
           }
//        }
//    });
})(jQuery);

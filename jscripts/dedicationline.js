(function($){
  "use strict";
  window.DedicationLine = window.DedicationLine || {};

  /* =======================
     1. Select2 + Form Logic
     ======================= */
  DedicationLine.initForm = function(cfg){
    if(typeof use_xmlhttprequest==="undefined"||use_xmlhttprequest!=1) return;
    if(typeof MyBB==="undefined") window.MyBB={};
    MyBB.select2();

    var allowMultiple = cfg.multirec == 1;
    var placeholderText = allowMultiple ? cfg.lang.search_users : cfg.lang.search_user;

    function initSelect2(){
      if($("#to").data("select2")) return;

      $("#to").select2({
        placeholder: placeholderText,
        minimumInputLength: 2,
        multiple: allowMultiple,
        maximumSelectionSize: cfg.maxrec,
        ajax: {
          url: "xmlhttp.php?action=get_users",
          dataType: "json",
          data: function(term){ return { query: term }; },
          results: function(data){ return { results: data }; }
        },
        // ✅ Prefill existing recipients (Select2 v3)
        initSelection: function(element, callback){
          var raw = $(element).val();
          if(!raw || typeof raw !== "string" || raw.trim() === ""){
            callback(allowMultiple ? [] : null);
            return;
          }

          var cleaned = raw.replace(/\s*,\s*/g, ",");
          var parts = cleaned.split(",");
          var results = [];

          $.each(parts, function(_, name){
            name = $.trim(name);
            if(name.length > 0){
              results.push({ id: name, text: name });
            }
          });

          if (allowMultiple)
            callback(results);
          else
            callback(results.length ? results[0] : null);
        }
      });

      $(".select2-container").css("z-index","9999");
    }

	function toggleBox(forceInit){
	  const box = $("#recipient_box");
	  const container = document.querySelector('.dedicationline-container');

	  function adjustContainerHeight(){
		if(!container) return;
		// Expand height to fit new content
		container.style.height = container.scrollHeight + 'px';
	  }

	  if($("#rec_specific").is(":checked")){
		box.slideDown(150, function(){
		  if(forceInit || !$("#to").data("select2")){
			initSelect2();
		  }

		  // Temporarily allow overflow and update height
		  if(container){
			container.style.overflow = 'visible';
			adjustContainerHeight();
			setTimeout(() => {
			  container.style.overflow = 'hidden';
			  adjustContainerHeight();
			}, 600);
		  }

		  // Reflow page
		  setTimeout(() => {
			window.scrollBy(0, 1);
			window.scrollBy(0, -1);
		  }, 250);
		});
	  } else {
		box.slideUp(150, function(){
		  $("#to").val("").trigger("change");
		  if(container){
			container.style.overflow = 'visible';
			adjustContainerHeight();
			setTimeout(() => {
			  container.style.overflow = 'hidden';
			  adjustContainerHeight();
			}, 600);
		  }

		  setTimeout(() => {
			window.scrollBy(0, 1);
			window.scrollBy(0, -1);
		  }, 250);
		});
	  }
	}

    $("input[name='recipient_type']").on("change", function(){
      toggleBox(true);
    });

    // ---------- Detect edit mode ----------
    var existingVal = $("#to").val() ? $.trim($("#to").val()) : "";
    if(existingVal.length > 0){
      // Editing an existing dedication
      $("#rec_specific").prop("checked", true);
      $("#rec_all").prop("checked", false);
      $("#recipient_box").show();
      initSelect2();
      // Ensure names are visible immediately
      setTimeout(function(){ $("#to").trigger("change"); }, 100);
    }
    else if($("#rec_specific").is(":checked")){
      $("#recipient_box").show();
      initSelect2();
    } else {
      $("#recipient_box").hide();
    }
  };

  /* =======================
     2. Mod Panel Transition
     ======================= */
  DedicationLine.initModPanel = function(){
    const container = document.querySelector('.dedicationline-container');
    if(!container) return;
    const manageBtn = document.getElementById('dl_togglebtn');
    const backBtn = document.getElementById('dl_backbtn');
    const STORAGE_KEY = 'dl_modview_open';

    function adjustContainerHeight(){
      const activeView = container.querySelector(
        container.classList.contains('show-modview') ? '.dl-modview' : '.dl-mainview'
      );
      if(activeView){
        container.style.height = activeView.scrollHeight + 'px';
      }
    }

    function showModView(){
      container.classList.add('show-modview');
      localStorage.setItem(STORAGE_KEY,'1');
      adjustContainerHeight();
    }
    function showMainView(){
      container.classList.remove('show-modview');
      localStorage.setItem(STORAGE_KEY,'0');
      adjustContainerHeight();
    }

    if(manageBtn) manageBtn.addEventListener('click', showModView);
    if(backBtn) backBtn.addEventListener('click', showMainView);

    if(localStorage.getItem(STORAGE_KEY)==='1'){
      container.classList.add('show-modview','no-transition');
      requestAnimationFrame(()=>container.classList.remove('no-transition'));
    }

    adjustContainerHeight();
    window.addEventListener('resize', adjustContainerHeight);
  };

  /* =======================
     3. Claim Banner Logic
     ======================= */
  DedicationLine.initClaimBanner = function(opts){
    const banner = document.getElementById('dedicationline-claim-banner');
    const closeBtn = document.getElementById('dl-claim-dismiss');
    if(!banner) return;

    const key = 'dl_claim_dismissed_session';
    const metaKey = 'dl_claim_meta';
    const currentIP = opts.ip;
    const currentCount = opts.count;

    let meta = {};
    try { meta = JSON.parse(sessionStorage.getItem(metaKey)) || {}; } catch(e){ meta = {}; }

    // Reset dismissal if IP or count changed
    if (meta.ip !== currentIP || meta.count < currentCount) {
      sessionStorage.removeItem(key);
    }
    sessionStorage.setItem(metaKey, JSON.stringify({ip: currentIP, count: currentCount}));

    if (sessionStorage.getItem(key)) {
      banner.style.display = 'none';
    }

    if (closeBtn) {
      closeBtn.addEventListener('click', function(){
        banner.style.display = 'none';
        sessionStorage.setItem(key, '1');
      });
    }
  };

  /* =======================
     4. Live Character Counter
     ======================= */
  DedicationLine.initCounter = function() {
    const textarea = document.getElementById('dedication_message');
    const counter  = document.getElementById('msg_counter');
    if(!textarea || !counter) return;

    textarea.addEventListener('input', function(){
      const max = parseInt(textarea.getAttribute('maxlength')) || 0;
      const len = textarea.value.length;
      counter.textContent = len + ' / ' + max;

      if(len > max * 0.9) counter.style.color = '#b91c1c';
      else if(len > max * 0.7) counter.style.color = '#d97706';
      else counter.style.color = '#666';
    });

    textarea.dispatchEvent(new Event('input'));
  };

  /* =======================
     5. Auto-init by context
     ======================= */
  $(function(){
    if($('#dedication_message').length){
      DedicationLine.initForm(window.DedicationLineConfig || {});
      DedicationLine.initCounter();
    }
    if($('.dedicationline-container').length){
      DedicationLine.initModPanel();
    }
  });

})(jQuery);

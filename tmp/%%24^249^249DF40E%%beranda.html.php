<?php /* Smarty version 2.6.13, created on 2012-05-28 12:02:35
         compiled from SITTI/beranda.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'capitalize', 'SITTI/beranda.html', 166, false),array('modifier', 'date_format', 'SITTI/beranda.html', 167, false),)), $this); ?>
<?php if ($this->_tpl_vars['xcid']): ?>
<script>var xcid = '<?php echo $this->_tpl_vars['xcid']; ?>
';</script>
<?php endif; ?>

	<?php echo '
   <script type="text/javascript">
		$.query = { numbers: false, hash: true };
		var tr = null;
		</script>
		<script type="text/javascript" src="js/jquery.query.js"></script>
		<script type="text/javascript" src="js/jquery.timers.js"></script>
    <script type="text/javascript" src="js/charts/highcharts.js"></script>
   <script>


 //On Click Event
  /* $("ul.tabjs li").click(function() {
	alert("hit nih !");
       $("ul.tabjs li").removeClass("active"); //Remove any "active" class
       $(this).addClass("active"); //Add "active" class to selected tab
       $(".tab_content").hide(); //Hide all tab content
		
       var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
       
       $(activeTab).fadeIn(); //Fade in the active ID content
       return false;
   });
   */
   function onCampaignDetail(k){
	   tr = k;
	   onTab("#PerformaIklan","nav6");
	   return false;
   }
   function onTab(activeTab,obj){
	 //On Click Event
		 $(document).stopTime();
	   $("ul.tabjs li").removeClass("active"); //Remove any "active" class
      $("ul.tabjs li."+obj).addClass("active").show(); //Add "active" class to selected tab
       $(".tab_content").hide(); //Hide all tab content
       $(activeTab).fadeIn(); //Fade in the active ID content
	    //alert("fpp-->"+activeTab);
		switch(activeTab){
		
			case "#Beranda":
				$("#tab01").html("<span style=\'text-align:center;\' align=\'center\'><img src=\'images/loader.gif\'/></span>");
				$.ajax({url:"report.php?t=11",
					async:true,
					timeout:15000,
					global:false,
					success:function(e){
					$("#tab01").html(e);
						
					}});
		  		/*$("#tab01").load("report.php", {"t":"1"},function(response, status, xhr) {
		  		  if (status == "error") {
		  		    var msg = "Maaf, tidak berhasil menghubungi server.";
		  		    $("#tab01").html(msg + xhr.status + " " + xhr.statusText);
		  		  }
		  		});
		  		*/
		  		/*$("#tab01").ajax({url:"report.php?t=1",
		  							async:false,
			  						success:function(e){
		  			//$("#tab01").html("");
			  				  			alert(e);
		  		}});*/
		  		
			break;
			case "#PerformaAkun":
				$("#tab02").html("<span style=\'text-align:center;\' align=\'center\'><img src=\'images/loader.gif\'/></span>");
		  		$("#tab02").load("report.php?t=2");
			break;
			case "#PerformaPenempatanIklan":
				$("#tab03").html("<span style=\'text-align:center;\' align=\'center\'><img src=\'images/loader.gif\'/></span>");
		  		$("#tab03").load("report.php?t=3");
			break;
			case "#PerformaKataKunci":
				$("#tab04").html("<span style=\'text-align:center;\' align=\'center\'><img src=\'images/loader.gif\'/></span>");
		  		$("#tab04").load("report.php?t=4");
			break;
			case "#PerformaTujuanIklan":
				$("#tab05").html("<span style=\'text-align:center;\' align=\'center\'><img src=\'images/loader.gif\'/></span>");
		  		$("#tab05").load("report.php?t=5");
			break;
			case "#PerformaIklan":
        		//var tr = $.query.get(\'tr\');
				$("#tab06").html("<span style=\'text-align:center;\' align=\'center\'><img src=\'images/loader.gif\'/></span>");
				if (tr==null){
					$("#tab06").load("report.php?t=6&tr=" + xcid);
				}else{
					$("#tab06").load("report.php?t=6&tr=" + tr);
				}
			break;
			case "#PerformaGeografis":
				$("#tab07").html("<span style=\'text-align:center;\' align=\'center\'><img src=\'images/loader.gif\'/></span>");
		  		$("#tab07").load("report.php?t=7");
			break;
			case "#InformasiPenagihan":
				$("#tab09").html("<span style=\'text-align:center;\' align=\'center\'><img src=\'images/loader.gif\'/></span>");
		  		$("#tab09").load("report.php?t=8");
			break;
			case "#topup":
				$("#tab10").html("<span style=\'text-align:center;\' align=\'center\'><img src=\'images/loader.gif\'/></span>");
		  		$("#tab10").load("report.php?t=99");
			break;
      case "#konversi":
        $("#tab11").html("<span style=\'text-align:center;\' align=\'center\'><img src=\'images/loader.gif\'/></span>");
          $("#tab11").load("report.php?t=konversi");
      break;
			default:
				$("#tab08").html("<span style=\'text-align:center;\' align=\'center\'><img src=\'images/loader.gif\'/></span>");
	  			$("#tab08").load("report.php?t=1");
			break;
		}
       
       return false;
   }
   function printCSV2(){
	   $("#tab02").load("report.php?t=201&csv=1");
	   return false;
   }
   function show_detail(iklan_id,sittiID,type,campaign_id,startFrom,endTo){
	    var data_url = "report.php?t=601&id="+iklan_id+"&campaign_id="+campaign_id+"&type="+type;
      if (typeof startFrom != \'undefined\') data_url += "&startFrom=" + escape(startFrom);
      if (typeof endTo != \'undefined\') data_url += "&endTo=" + escape(endTo);
      $("#tab06").html("<span style=\'text-align:center;\' align=\'center\'><img src=\'images/loader.gif\'/></span>");
 		  $("#tab06").load(data_url, 
        function(data) {
          $("#tab06 select#rtype").val(type);
          $("#tab06 a#exportcsv").attr("href", data_url + "&csv=1");
          if (type == 6) {
            $(\'.pilihHariBeranda\').show();
            if (typeof startFrom != \'undefined\' && typeof endTo != \'undefined\') {
              $("#tab06 input#tanggalAwalBeranda").val(startFrom);
              $("#tab06 input#tanggalAkhirBeranda").val(endTo); 
            }
          }
      });
	}
	function openUrl(uri,obj){
		$("#"+obj).html("<span style=\'text-align:center;\' align=\'center\'><img src=\'images/loader.gif\'/></span>");
		$("#"+obj).load(uri);
	}
   </script>
   '; ?>

              <ul class="tabjs">
                <li class="nav1"><a href="javascript:void(0);" onClick="onTab('#Beranda','nav1');">Beranda</a></li>
                <li class="nav2"><a href="javascript:void(0);" onClick="onTab('#PerformaAkun','nav2');">Performa Kampanye</a></li>
                <!--li class="nav6"><a href="javascript:void(0);" onClick="onTab('#PerformaIklan','nav6');">Performa Iklan</a></li-->
                <li class="nav3"><a href="javascript:void(0);" onClick="onTab('#PerformaKataKunci','nav3');">Performa Kata Kunci</a></li>
               <!-- <li class="nav4"><a href="javascript:void(0);" onClick="onTab('#PerformaPenempatanIklan','nav4');">Performa Penempatan Iklan</a></li>-->
                <!-- <li><a href="javascript:void(0);" onClick="onTab('#PerformaTujuanIklan');">Performa Tujuan Iklan</a></li>-->
                <!--<a href="javascript:void(0);" onClick="onTab('#PerformaGeografis');">Performa Geografis</a></li>-->
                <!--li class="nav9"><a href="javascript:void(0);" onClick="onTab('#InformasiPenagihan','nav9');">Mutasi Rekening</a></li-->
                <!--li class="nav10"><a href="pembayaran.php" >Informasi Top Up</a></li-->
                <!--li class="nav11"><a href="javascript:void(0);" onClick="onTab('#konversi','nav11');">Konversi</a></li-->
              </ul>
              <div class="tab_container">
              <?php if ($this->_tpl_vars['BILLING_READY'] <> '1' || ! $this->_tpl_vars['BUDGET_BIGGER_THAN_MINIMUM_BID']): ?>
                <div class="peringatan">
                    <a href="pembayaran.php"><?php if ($this->_tpl_vars['BILLING_READY'] <> '1'): ?>Peringatan : Saldo akun Anda Kosong.<?php endif; ?> <strong>Klik disini</strong> untuk melakukan top-up akun. <?php if (! $this->_tpl_vars['BUDGET_BIGGER_THAN_MINIMUM_BID']): ?>Iklan Anda tidak dapat berjalan apabila saldo Anda kurang dari harga CPC minimal.<?php endif; ?></a>
                </div>
                <?php endif; ?>
                <div id="Beranda" class="tab_content">
                  <div class="shorter">
                    <h1>Selamat Datang, <?php echo ((is_array($_tmp=$this->_tpl_vars['account_name'])) ? $this->_run_mod_handler('capitalize', true, $_tmp) : smarty_modifier_capitalize($_tmp)); ?>
</h1>
                    <span style="float:right">Login terakhir anda: <?php echo ((is_array($_tmp=$this->_tpl_vars['last_login'])) ? $this->_run_mod_handler('date_format', true, $_tmp, "%e/%m/%Y") : smarty_modifier_date_format($_tmp, "%e/%m/%Y")); ?>
</span>
                    <!--
                    <form>
                    <div class="goSubmit">
                    	<input type="submit" class="submitButton" value="GO" />
                    </div>
                    <div id="custom">
                      <div class="pilihHariBeranda">
                       	<input id="tanggalAwalBeranda" type="text">
                        <span>s/d</span>
                        <input id="tanggalAkhirBeranda" type="text">
                      </div>
                    </div>
                    <div class="pilihWaktu">
                      <select name="ad_type" id="pilihWaktuBeranda" onchange="wiz_dirty(); updateCodeField()">
                        <option>Hari Ini</option>
                        <option>Kemarin</option>
                        <option>3 Hari Lalu</option>
                        <option>7 Hari Lalu</option>
                        <option>14 Hari Lalu</option>
                        <option>30 Hari Lalu</option>
                        <option value="0">Custom</option>
                      </select>
                    </div>
                    </form>  -->
                  </div>
                  <div id="tab01" class="tab_pad">
                    
                  </div>
                </div>
                <div id="PerformaAkun" class="tab_content">
                  <div class="shorter">
                    <h1>Performa Akun & Kampanye Anda</h1> <a style="float:right;position:relative;" href="beranda.php?buat_campaign=1" class="submitButton">Buat Kampanye</a>
                  </div>
                  <div id="tab02" class="tab_pad">
                   </div>
                </div>
                <div id="PerformaPenempatanIklan" class="tab_content">
                  <div class="shorter">
                    <h1> Performa Penempatan</h1>
                  </div>
                  <div id="tab03" class="tab_pad">
                    
                  </div>
                </div>
                <div id="PerformaKataKunci" class="tab_content">
                  <div class="shorter">
                    <h1>Performa Kata Kunci</h1>
                  </div>
                  <div id="tab04" class="tab_pad">
                   
                   
                   </div>
                </div>
                <div id="PerformaTujuanIklan" class="tab_content">
                  <div class="shorter">
                    <h1>Performa Tujuan Iklan</h1>
                    <form>
                    <div class="goSubmit">
                    	<input type="submit" class="submitButton" value="GO" />
                    </div>
                    <div id="custom">
                      <div class="pilihHariTujuan">
                       	<input id="tanggalAwalTujuan" type="text">
                        <span>s/d</span>
                        <input id="tanggalAkhirTujuan" type="text">
                      </div>
                    </div>
                    <div class="pilihWaktu">
                      <select name="ad_type" id="pilihWaktuTujuan" onchange="wiz_dirty(); updateCodeField()">
                        <option>Hari Ini</option>
                        <option>Kemarin</option>
                        <option>3 Hari Lalu</option>
                        <option>7 Hari Lalu</option>
                        <option>14 Hari Lalu</option>
                        <option>30 Hari Lalu</option>
                        <option value="4">Custom</option>
                      </select>
                    </div>
                    </form>
                  </div>
                  <div id="tab05" class="tab_pad">
                  
                   </div>
                </div>
                <div id="PerformaIklan" class="tab_content">
                  <div class="shorter">
                    <h1>Performa  Iklan</h1>
                    <span id="link"></span>
                 <!--<form>
                    <div class="goSubmit">
                    	<input type="submit" class="submitButton" value="GO" />
                    </div>
                    <div id="custom">
                      <div class="pilihHariPeforma">
                       	<input id="tanggalAwalPeforma" type="text">
                        <span>s/d</span>
                        <input id="tanggalAkhirPeforma" type="text">
                      </div>
                    </div>
                    <div class="pilihWaktu">
                      <select name="ad_type" id="pilihWaktuPeforma" onchange="wiz_dirty(); updateCodeField()">
                        <option>Hari Ini</option>
                        <option>Kemarin</option>
                        <option>3 Hari Lalu</option>
                        <option>7 Hari Lalu</option>
                        <option>14 Hari Lalu</option>
                        <option>30 Hari Lalu</option>
                        <option value="5">Custom</option>
                      </select>
                    </div>
                    </form>-->
                  </div>
                  <div id="tab06" class="tab_pad">
                   
                   
                   </div>
                </div>
                <div id="PerformaGeografis" class="tab_content">
                  <div class="shorter">
                    <h1>Performa Geografis (TOP 5 Asal Geografis)</h1>
           		    <form>
                    <div class="goSubmit">
                    	<input type="submit" class="submitButton" value="GO" />
                    </div>
                    <div id="custom">
                      <div class="pilihHariGeografis">
                       	<input id="tanggalAwalGeografis" type="text">
                        <span>s/d</span>
                        <input id="tanggalAkhirGeografis" type="text">
                      </div>
                    </div>
                    <div class="pilihWaktu">
                      <select name="ad_type" id="pilihWaktuGeografis" onchange="wiz_dirty(); updateCodeField()">
                        <option>Hari Ini</option>
                        <option>Kemarin</option>
                        <option>3 Hari Lalu</option>
                        <option>7 Hari Lalu</option>
                        <option>14 Hari Lalu</option>
                        <option>30 Hari Lalu</option>
                        <option value="6">Custom</option>
                      </select>
                    </div>
                    </form>
                  </div>
                  <div id="tab07" class="tab_pad">
                    
                    </div>
                </div>
				        <div id="InformasiPenagihan" class="tab_content">
                  <div class="shorter">
                    <h1>Mutasi Rekening</h1>
                  </div>
                  <div id="tab09" class="tab_pad">
                    
                  </div>
                </div>
                <div id="topup" class="tab_content">
                  <div class="shorter">
                    <h1>Cara Top Up Akun SITTI Anda</h1>
                  </div>
                  <div id="tab10" class="tab_pad">
                    
                  </div>
                </div>
                <div id="konversi" class="tab_content">
                  <div class="shorter">
                    <h1>Konversi</h1>
                  </div>
                  <div id="tab11" class="tab_pad">
                  </div>
                </div>
              </div>
        <script>
        <?php echo '
       
        $(document).ready(function() {
            var s = $.query;
            var url_param = location.search;
            var param_performaiklan = /PerformaIklan/gi;
			var param_performakatakunci = /PerformaKataKunci/gi;
           
            if(s=="#PerformaAkun"){
            	onTab("#PerformaAkun","nav2");
            }else if(s=="#PerformaKataKunci"){
            	onTab("#PerformaKataKunci","nav3");
            }else if((url_param != \'\') && (url_param.match(param_performakatakunci) != null)){
              onTab("#PerformaKataKunci","nav3");
            }else if((url_param != \'\') && (url_param.match(param_performaiklan) != null)){
              onTab("#PerformaIklan","nav6");
            }else if(s=="#InformasiPenagihan"){
            	onTab("#InformasiPenagihan","nav9");
            }else if(s=="?PerformaAkun"){
            	onTab("#PerformaAkun","nav2");
            }else if(s=="?PerformaIklan"){
            	onTab("#PerformaIklan","nav6");
            }
            else if(s=="?InformasiPenagihan"){
            	onTab("#InformasiPenagihan","nav9");
            }else if(s=="?topup"){
            	onTab("#topup","nav10");
            }else if(s=="#topup"){
            	onTab("#topup","nav10");
            }
            else{
                
            	onTab("#Beranda","nav1");
            }
        });
        '; ?>

        </script>
               
<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/new/common/Common.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/new/common/StringUtil.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/new/common/DBUtil.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/new/common/FileUtil.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/new/common/Logger.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/new/sms/smsProc.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/nm/common/FrontCommon.php");
$counselGbn = getParam("counselGbn", "25718");
?>
<!DOCTYPE html>
<html class="no-js" lang="ko">

<head>
  <?
  require_once($_SERVER["DOCUMENT_ROOT"] . "/nm/common/meta_k.php");
  ?>
  <!-- 웹으로 모바일 페이지 보면 리다이렉트 -->
  <script>
    if ($(window).width() > 780) {
      $.getScript('js/nbw-parallax.js');
    }
    if (window.innerWidth > 780) {
      //Your Code
      window.location.href = 'https://www.isoohyun.co.kr/new/lovetest/first_face_test.php?counselGbn=<?= $counselGbn ?>&mctkey=<?= $mctkey ?>';
    }
  </script>
  <script src="https://developers.kakao.com/sdk/js/kakao.js"></script>
  <script>
    Kakao.init('16b3c92425889edb797d2dc78b3d1428'); // 발급받은 키 중 javascript키를 사용해준다.
    //카카오 정보 가져오기 (type_name 변수 파라미터로 받음)
    function kakaoGetData(type_name) {
      Kakao.Auth.login({
        success: function(response) {
          console.log(response);
          Kakao.API.request({
            url: '/v2/user/me',
            success: function(response) {
              var user_id = "k_" + response.id; // 아이디
              var birthyear = response.kakao_account.birthyear; // 생일
              var email = response.kakao_account.email; // 이메일
              var gender = response.kakao_account.gender; // 성별
              if (gender == 'male') { // DB에 맞는 성별처리
                gender = '1';
              } else {
                gender = '2';
              }
              var phone_number = response.kakao_account.phone_number; // 핸드폰번호
              var phone_number = phone_number.replace('+82 ', '0'); // 핸드폰 앞자리 치환
              var nickname = response.properties.nickname; // 카카오톡 닉네임

              $('#user_id').val(user_id);
              $('#birthday').val(birthyear);
              $('#email').val(email);
              $('#gender').val(gender);
              $('#phone').val(phone_number);
              $('#name').val(nickname);
              // 타입네임 변수 전달
              $('#type_name2').val(type_name);

            },
            fail: function(error) {
              console.log(error)
            },
          })
          Kakao.API.request({
            url: '/v1/user/shipping_address',
            success: function(response) { // 우선 첫번째 등록한 주소를 불러오도록...
              var base_address = response.shipping_addresses[0].base_address;
              var detail_address = response.shipping_addresses[0].detail_address;
              var zone_number = response.shipping_addresses[0].zone_number;
              $('#area').val(base_address);
              $('#area_post_number').val(zone_number); //신주소 우편번호
              //$('#detail_address').val(detail_address);
              //$('#zone_number').val(zone_number);
            },
            fail: function(error) {
              console.log(error)
            },
          })
          // 카카오 정보 및 form 정보 넘기는 부분
          setTimeout(function() {
            $('#frm').validate({
              success: function() {
                this.target = "counselResult";
                this.action = "/new/common/first_face_test_proc.php";
                this.submit();
                var name = $('input[name=name]').val();
                var email = $('input[name=email1]').val() + '@' + $('input[name=email2]').val();
                $('#resultName').html($('input[name=name]').val());
                $('#resultEmail').html($('input[name=email]').val());
                $("#resultPhone").html($('input[name=phone]').val());
              }
            });
          }, 1000);
        },
        fail: function(error) {
          console.log(error)
        },
      })
    }

    // first_face type
    var type01 = '';
    var type02 = '';
    var type03 = '';
    var type04 = '';
    var type05 = '';
    var type = [];
    var type_name = '';

    //페이지 열릴 때 show(0)으로 이동
    $(document).ready(function() {
      show(0);
    });

    // show()함수
    function show(idx, cmd, txt) {
      if (idx == 2) {
        if ($('#school').val() == "") {
          alert("학력을 선택해주세요");
          return false;
        } else if ($('select[name=new_birthday]').val() == "") {
          alert('출생년도를 선택해주세요.');
          $('select[name=new_birthday]').focus();
          return;
        }
      } else if (idx == 3) {
        type01 = txt;
        type[0] = type01;
        console.log(type01);
      } else if (idx == 4) {
        type02 = txt;
        type[1] = type02;
        console.log(type02);
      } else if (idx == 5) {
        type03 = txt;
        type[2] = type03;
        console.log(type03);
      } else if (idx == 6) {
        type04 = txt;
        type[3] = type04;
        console.log(type04);
      } else if (idx == 7) {
        type05 = txt;
        type[4] = type05;
        console.log(type05);
      } else if (idx == 8) { // 카카오 결과 페이지
        console.log(type);

        // 배열에서 'a,b,c,d' 갯수 구하기
        let type_a = 0;
        let type_b = 0;
        let type_c = 0;
        let type_d = 0;
        // a타입 갯수
        for (let i = 0; i < type.length; i++) {
          if (type[i] === 'a') {
            type_a++;
          }
        }
        console.log(type_a);
        // b타입 갯수
        for (let i = 0; i < type.length; i++) {
          if (type[i] === 'b') {
            type_b++;
          }
        }
        console.log(type_b);
        // c타입 갯수
        for (let i = 0; i < type.length; i++) {
          if (type[i] === 'c') {
            type_c++;
          }
        }
        console.log(type_c);
        // d타입 갯수
        for (let i = 0; i < type.length; i++) {
          if (type[i] === 'd') {
            type_d++;
          }
        }
        console.log(type_d);
        // 숫자 값 최대 최소 판단
        var type_hight = Math.max(type_a, type_b, type_c, type_d);
        var type_min = Math.min(type_a, type_b, type_c, type_d);
        // 중복된 값 있을 때 최저 경우로 아닐 경우 최대 선택 경우로 이동 
        if (type_hight === type_a) {
          type_name = "솔직한사람";
        } else if (type_hight === type_b) {
          type_name = "센스있는사람";
        } else if (type_hight === type_c) {
          type_name = "차가운사람";
        } else if (type_hight === type_d) {
          type_name = "재밌는사람";
        }
        console.log("결과 : " + type_name);
      }

      // type 결과에 따라서 페이지 section 처리 함
      if (this.type_name == "솔직한사람") {
        $('section').hide();
        $('.a').show();
      } else if (this.type_name == "센스있는사람") {
        $('section').hide();
        $('.b').show();
      } else if (this.type_name == "차가운사람") {
        $('section').hide();
        $('.c').show();
      } else if (this.type_name == "재밌는사람") {
        $('section').hide();
        $('.d').show();
      } else {
        $('section').hide();
        $('section:eq(' + idx + ')').show();
      }
    }

    //함수 한번만 실행 변수
    var is_action = false;

    function success() {
      show(8);

      // $('#frm').get(0).reset();
      // 카카오 버튼 눌렀을 때 type_name변수를 kakaogetdata로 인자값 전송 (한번만 실행)
      if (is_action === true) {
        return false;
      }
      is_action = true;
      kakaoGetData(type_name);
    }

    // select시 색 변경
    function changecolor1() {
      $(".new_birthday2").css("background-color", "#FF5675");
      $(".new_birthday2").css("border", "1px solid white");
      $(".new_birthday2").css("color", "white");
      console.log("change1");
    }

    function changecolor2() {
      $(".school2").css("background-color", "#FF5675");
      $(".school2").css("border", "1px solid white");
      $(".school2").css("color", "white");
      console.log("change2");
    }
  </script>
  <!-- p text -->
  <style>
    .p_text {
      text-align: center;
      font-size: 20px;
      padding-top: 20px;
      padding-bottom: 20px;
      color: #ff5193;
    }

    .radio-box {
      list-style: none;
    }

    .list_box {
      margin-left: 5px;
      float: left;
      width: 35%;
      border: 3px solid white;
      text-align: center;
      padding: 8% 5%;
      font-size: 15px;
      color: black;
      background-color: whitesmoke;
      opacity: 0.5;
    }

    .list_box:hover {
      border: 3px solid #FF5675;
      background-color: #ff6688;
      color: white;
      float: left;
      width: 35%;
      text-align: center;
      padding: 8% 5%;
      font-size: 15px;
      opacity: 0.5;
    }

    .join-charge {
      background-color: white;
    }

    input[id="gender1"]+label {
      width: 80%;
      border: 3px solid white;
      text-align: center;
      padding: 7% 14%;
      font-size: 20px;
      color: black;
      background-color: whitesmoke;
      opacity: 0.5;
      margin-bottom: 30px;
    }

    input[id="gender1"]:checked+label {
      width: 80%;
      border: 3px solid #FF5675;
      text-align: center;
      padding: 7% 14%;
      font-size: 20px;
      color: white;
      background-color: #ff6688;
      opacity: 0.7;
      margin-bottom: 30px;
    }

    input[id="gender2"]+label {
      width: 80%;
      border: 3px solid white;
      text-align: center;
      padding: 7% 14%;
      font-size: 20px;
      color: black;
      background-color: whitesmoke;
      opacity: 0.7;
      margin-bottom: 30px;
    }

    input[id="gender2"]:checked+label {
      width: 80%;
      border: 3px solid #FF5675;
      text-align: center;
      padding: 7% 14%;
      font-size: 20px;
      color: white;
      background-color: #ff6688;
      opacity: 0.7;
      margin-bottom: 30px;
    }

    input[id="marry1"]+label {
      width: 80%;
      border: 3px solid white;
      text-align: center;
      padding: 7% 14%;
      font-size: 20px;
      color: black;
      background-color: whitesmoke;
      opacity: 0.7;
      margin-bottom: 30px;
    }

    input[id="marry1"]:checked+label {
      width: 80%;
      border: 3px solid #FF5675;
      text-align: center;
      padding: 7% 14%;
      font-size: 20px;
      color: white;
      background-color: #ff6688;
      opacity: 0.7;
      margin-bottom: 30px;
    }

    input[id="marry2"]+label {
      width: 80%;
      border: 3px solid white;
      text-align: center;
      padding: 7% 14%;
      font-size: 20px;
      color: black;
      background-color: whitesmoke;
      opacity: 0.7;
      margin-bottom: 30px;
    }

    input[id="marry2"]:checked+label {
      width: 80%;
      border: 3px solid #FF5675;
      text-align: center;
      padding: 7% 14%;
      font-size: 20px;
      color: white;
      background-color: #ff6688;
      opacity: 0.7;
      margin-bottom: 30px;
    }

    .new_birthday2 {
      width: 80%;
      border: 3px solid white;
      text-align: center;
      padding: 10%;
      font-size: 20px;
      color: black;
      background-color: whitesmoke;
      opacity: 0.5;
      margin-bottom: 30px;
    }


    .school2 {
      width: 80%;
      border: 3px solid white;
      text-align: center;
      padding: 10%;
      font-size: 20px;
      color: black;
      background-color: whitesmoke;
      opacity: 0.5;
      margin-bottom: 30px;
    }

    select {
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      text-indent: 15px;
    }
  </style>
</head>

<body>
  <div class="wrap">
    <?
    require_once($_SERVER["DOCUMENT_ROOT"] . "/nm/common/header.php");
    ?>
    <div id="container">
      <?
      require_once($_SERVER["DOCUMENT_ROOT"] . "/nm/common/pageTitle.php");
      ?>
    </div>
    <div id="layer_fixeds" class="phone-links">
      <a class="" href="/nm/common/Counseling.php"><i class=""></i>1:1문의</a>
      <a class="" href="/nm/common/brochure.php"><i class=""></i>브로셔신청</a>
      <a href="tel:025404000"><i class="ico-phone"></i>전화상담</a>
    </div>
    <!-- 시작부분 show(0) -->
    <section id="lovetest">
      <div class="join-charge">
        <div class="" style="background-image: url('/nm/image/first_face/m_start2.png'); height:540px; background-size: contain;background-repeat: no-repeat;background-position:center;">
          <p class="btn"><a href="javascript:show(1);"><img style="width: 90%; margin-bottom:-20px;" src="/nm/image/first_face/index_btn.png" alt="" /></a></p>
        </div>
      </div>
    </section>

    <section id="lovetest">
      <form id="frm" name="frm" method="post">
        <input type="hidden" name="counselGbn" value="<?= getParam("counselGbn", "25718") ?>" />
        <input type="hidden" name="counselGbn2" value="첫인상 테스트" />
        <input type="hidden" name="type_name2" id="type_name2" value="" />
        <input type="hidden" name="marriage" value="10501" />
        <input type="hidden" id="name" name="name">
        <input type="hidden" id="gender" name="gender">
        <input type="hidden" id="birthday" name="birthday">
        <input type="hidden" id="area" name="area">
        <input type="hidden" id="phone" name="phone">
        <input type="hidden" id="email" name="email">
        <input type="hidden" name="content" />
        <input type="hidden" name="user_id" id="user_id" />
        <input type="hidden" id="area_post_number" name="area_post_number">

        <!-- show(1) -->
        <div class="join-charge">
          <div style="background-image: url('/nm/image/first_face/mobile_bg01.png'); height:600px;">
            <div class="input-box">
              <div>
                <center>
                  <img style="margin-top: 10px;" src="/nm/image/first_face/m_p_q_top_img.png" alt="" />
                </center>
              </div>
              <p class="p_text">나의 정보 입력하고 시작하기</p>
              <div style="text-align: center; margin-top:30px;">
                <div>
                  <input id="gender1" type="radio" name="gender" value="1" style="display: none;" /> <label for="gender1">남성</label>&nbsp;&nbsp;
                  <input id="gender2" type="radio" name="gender" value="2" style="display: none;" /><label for="gender2">여성</label>
                </div><br><br>
                <div style="padding-top: 20px;">
                  <input id="marry1" type="radio" name="marriage" value="10501" style="display: none;" /><label for="marry1"> 초혼</label>&nbsp;&nbsp;
                  <input id="marry2" type="radio" name="marriage" value="10502" style="display: none;" /> <label for="marry2">재혼</label>
                </div><br><br>
                <div>
                  <select onchange="changecolor1();" id="new_birthday" name="new_birthday" class="new_birthday2" style="height: 50px;">
                    <option value="">출생년도</option>
                    <? for ($i = 1950; $i < date('Y'); $i++) { ?>
                      <option value="<?= $i ?>"><?= $i; ?>년</option>
                    <? } ?>
                  </select>
                </div>
                <div style="margin-top: 0px;">
                  <select onchange="changecolor2();" id="school" name="school" class="school2" message="학력을 선택해주세요." style="height: 50px;">
                    <option value="">학력</option>
                    <option value=" 대학(2, 3년제) 재학">대학(2, 3년제) 재학</option>
                    <option value="대학(2, 3년제) 졸업">대학(2, 3년제) 졸업</option>
                    <option value="대학(4년제) 재학">대학(4년제) 재학</option>
                    <option value="대학(4년제) 졸업">대학(4년제) 졸업</option>
                    <option value="대학원(석사) 재학">대학원(석사) 재학</option>
                    <option value="대학원(석사) 졸업">대학원(석사) 졸업</option>
                    <option value="대학원(박사) 재학">대학원(박사) 재학</option>
                    <option value="대학원(박사) 졸업">대학원(박사) 졸업</option>
                    <option value="고등학교 졸업">고등학교 졸업</option>
                    <option value="기타">기타</option>
                  </select>
                </div>
              </div>
            </div>
            <!-- 이전페이지, 다음페이지 -->
            <div style="margin-left:20px; display:block; margin-top:-420px">
              <img style="zoom: 0.7;" src="/nm/image/first_face/m_btn_prev.png" alt="" onclick="show(0);return false;" />
            </div>
            <center>
              <img src="/nm/image/first_face/m_btn_next.png" alt="" style="width: 80%; text-align:center; margin-top:420px;" onclick="show(2);return false;" />
            </center>
            <!-- 이전페이지, 다음페이지 -->
          </div>
        </div>
        <iframe src="" id="counselResult" name="counselResult" width="0" height="0" style="display:none;" frameborder="0"></iframe>
      </form>
    </section>

    <!-- show(2) -->
    <section id="lovetest">
      <div class="join-charge">
        <div class="" style="background-image: url('/new/image/first_face/p_q_bg01.png'); height:600px;">
          <div>
            <center>
              <img style="margin-top: 30px;" src="/new/image/first_face/p_q_top_img.png" alt="" />
            </center>
          </div>
          <p class="p_text">서점에 온 당신, 가장 마음에 드는것은?</p><br><br><br>
          <ul class="radio-box" style="width:80%; margin:auto;">
            <li class="list_box" onclick="show(3,'next','a');">좋아하는 <br>연예인 화보집</li>
            <li class="list_box" onclick="show(3,'next','b');">취미 등<br> 자기계발서</li>
            <li class="list_box" onclick="show(3,'next','c');">커리어에<br>도움될만한 서적</li>
            <li class="list_box" onclick="show(3,'next','d');">유머 <br>모음집</li>
          </ul>
          <!-- 이전페이지, 다음페이지 -->
          <div style="margin-left:20px; display:block; margin-top:-170px">
            <img style="zoom: 0.7;" src="/nm/image/first_face/m_btn_prev.png" alt="" onclick="show(1);return false;" />
          </div>
          <!-- 이전페이지, 다음페이지 -->
        </div>
      </div>
    </section>

    <!-- show(3) -->
    <section id="lovetest">
      <div class="join-charge">
        <div class="" style="background-image: url('/new/image/first_face/p_q_bg01.png'); height:600px;">
          <div>
            <center>
              <img style="margin-top: 30px;" src="/new/image/first_face/p_q_top_img.png" alt="" />
            </center>
          </div>

          <p class="p_text">데이트 상대와 보고싶은 영화 장르는?</p><br><br><br>
          <ul class="radio-box" style="width:80%; margin:auto;">
            <li class="list_box" onclick="show(4,'next','a');">코미디</li>
            <li class="list_box" onclick="show(4,'next','b');">로맨스</li>
            <li class="list_box" onclick="show(4,'next','c');">스릴러/무협</li>
            <li class="list_box" onclick="show(4,'next','d');">SF</li>
          </ul>
          <!-- 이전페이지, 다음페이지 -->
          <div style="margin-left:20px; display:block; margin-top:-170px">
            <img style="zoom: 0.7;" src="/nm/image/first_face/m_btn_prev.png" alt="" onclick="show(2);return false;" />
          </div>
          <!-- 이전페이지, 다음페이지 -->
        </div>
      </div>
    </section>

    <!-- show(4) -->
    <section id="lovetest">
      <div class="join-charge">
        <div class="" style="background-image: url('/new/image/first_face/p_q_bg01.png'); height:600px;">
          <div>
            <center>
              <img style="margin-top: 30px;" src="/new/image/first_face/p_q_top_img.png" alt="" />
            </center>
          </div>

          <p class="p_text">사람들이 보는 나의 이미지는?</p><br><br><br>
          <ul class="radio-box" style="width:80%; margin:auto;">
            <li class="list_box" onclick="show(5,'next','a');">귀엽다</li>
            <li class="list_box" onclick="show(5,'next','b');">똑똑하다</li>
            <li class="list_box" onclick="show(5,'next','c');">도도하다</li>
            <li class="list_box" onclick="show(5,'next','d');">재밌다</li>
          </ul>
          <!-- 이전페이지, 다음페이지 -->
          <div style="margin-left:20px; display:block; margin-top:-170px">
            <img style="zoom: 0.7;" src="/nm/image/first_face/m_btn_prev.png" alt="" onclick="show(3);return false;" />
          </div>
          <!-- 이전페이지, 다음페이지 -->
        </div>
      </div>
    </section>

    <!-- show(5) -->
    <section id="lovetest">
      <div class="join-charge">
        <div class="" style="background-image: url('/new/image/first_face/p_q_bg01.png'); height:600px;">
          <div>
            <center>
              <img style="margin-top: 30px;" src="/new/image/first_face/p_q_top_img.png" alt="" />
            </center>
          </div>

          <p class="p_text">어색한 자리 나는 주로 대화를?</p><br><br><br>
          <ul class="radio-box" style="width:80%; margin:auto;">
            <li class="list_box" onclick="show(6,'next','a');">주도하는<br> 편이다</li>
            <li class="list_box" onclick="show(6,'next','b');">적절히<br> 리액션을한다</li>
            <li class="list_box" onclick="show(6,'next','c');">묻는 말에만<br> 대답한다</li>
            <li class="list_box" onclick="show(6,'next','d');">그냥<br> 말이 많다</li>
          </ul>
          <!-- 이전페이지, 다음페이지 -->
          <div style="margin-left:20px; display:block; margin-top:-170px">
            <img style="zoom: 0.7;" src="/nm/image/first_face/m_btn_prev.png" alt="" onclick="show(4);return false;" />
          </div>
          <!-- 이전페이지, 다음페이지 -->
        </div>
      </div>
    </section>

    <!-- show(6) -->
    <section id="lovetest">
      <div class="join-charge">
        <div class="" style="background-image: url('/new/image/first_face/p_q_bg01.png'); height:600px;">
          <div>
            <center>
              <img style="margin-top: 30px;" src="/new/image/first_face/p_q_top_img.png" alt="" />
            </center>
          </div>

          <p class="p_text">첫 만남, 카페에서 내가 주문한 음료는?</p><br><br><br>
          <ul class="radio-box" style="width:80%; margin:auto;">
            <li class="list_box" onclick="show(7,'next','a');">아이스티</li>
            <li class="list_box" onclick="show(7,'next','b');">달달한 라떼</li>
            <li class="list_box" onclick="show(7,'next','c');">아메리카노</li>
            <li class="list_box" onclick="show(7,'next','d');">과일음료</li>
          </ul>
          <!-- 이전페이지, 다음페이지 -->
          <div style="margin-left:20px; display:block; margin-top:-170px">
            <img style="zoom: 0.7;" src="/nm/image/first_face/m_btn_prev.png" alt="" onclick="show(5);return false;" />
          </div>
          <!-- 이전페이지, 다음페이지 -->
        </div>
      </div>
    </section>

    <!-- show(7) 카카오로 결과 확인하기 -->
    <section id="lovetest">
      <div class="join-charge">
        <div class="" style="background-image: url('/nm/image/first_face/mobile_bg02.png'); height:540px; background-size: contain;background-repeat: no-repeat;background-position:center;">

          <div>
            <center>
              <img style="margin-top: 30px;" src="/new/image/first_face/p_q_top_img.png" alt="" />
            </center>
          </div>
          <p class="p_text" style="margin-top:-10px;">나의 첫인상은??</p><br>

          <div>
            <center>
              <img style="width:80%;  margin-top:240px; cursor:pointer;" src="/nm/image/first_face/btn_kakao.png" alt="" onclick="javascript:success();" />
            </center>
          </div>
          <!-- 이전페이지, 다음페이지 -->
          <div style="margin-left:20px; display:block; margin-top:-423px">
            <img style="zoom: 0.7;" src="/nm/image/first_face/m_btn_prev.png" alt="" onclick="show(6);return false;" />
          </div>
          <!-- 이전페이지, 다음페이지 -->
        </div>
      </div>
      <iframe src="" id="counselResult" name="counselResult" width="0" height="0" style="display:none;" frameborder="0"></iframe>
    </section>

    <!-- show(8) default -->
    <section id="lovetest">
      <div class="join-charge">
        결과처리
      </div>
    </section>

    <!-- show(9) a 결과 -->
    <section id="lovetest" class="a">
      <div class="join-charge">
        <div style="background-image: url('/nm/image/first_face/m_result.png'); height:550px; background-repeat: no-repeat; background-size: contain; background-position:center;">
          <div style="text-align:center;">
            <p style="font-size:30px; color:#ff5193; padding-top:172px;">솔직한 사람</p>
            <div style="padding-bottom:60px;"></div>
            <p style="font-size:17px;">항상 진심으로 대하며,<br><br>
              청량하고 밝은 느낌으로 보여요!<br><br>
              내면의 담백한 매력이 있을 것 같은 사람!
            </p>
          </div>
          <div style="cursor:pointer; margin-left:30px; display:block; margin-top:-360px;">
            <img style="zoom:0.7;" src="/nm/image/first_face/m_btn_re.png" alt="" onclick="location.reload();" />
          </div>
        </div>
      </div>
    </section>

    <!-- show(10) b 결과 -->
    <section id="lovetest" class="b">
      <div class="join-charge">
        <div style="background-image: url('/nm/image/first_face/m_result.png'); height:550px; background-repeat: no-repeat; background-size: contain; background-position:center;">
          <div style="text-align:center;">
            <p style="font-size:30px; color:#ff5193; padding-top:172px;">센스있는 사람</p>
            <div style="padding-bottom:60px;"></div>
            <p style="font-size:17px;">항상 누구보다<br><br>
              적극적으로 나설것 같아요<br><br>
              매사에 열정적이고<br><br>
              남들보다 앞서갈것 같은 이미지입니다!
            </p>
          </div>
          <div style="cursor:pointer; margin-left:30px; display:block; margin-top:-410px;">
            <img style="zoom:0.7;" src="/nm/image/first_face/m_btn_re.png" alt="" onclick="location.reload();" />
          </div>
        </div>
      </div>
    </section>

    <!-- show(11) c 결과 -->
    <section id="lovetest" class="c">
      <div class="join-charge">
        <div style="background-image: url('/nm/image/first_face/m_result.png'); height:550px; background-repeat: no-repeat; background-size: contain; background-position:center;">
          <div style="text-align:center;">
            <p style="font-size:30px; color:#ff5193; padding-top:172px;">차도남녀</p>
            <div style="padding-bottom:60px;"></div>
            <p style="font-size:17px;">표정이 잘 드러나지
              않는 탓에<br><br>
              늘 이성을 유지하고
              침착해보여요!<br><br>

              체계적이고 성실한 성격!?<br><br>
              가끔은 느슨해도
              괜찮겠지만요~
            </p>
          </div>
          <div style="cursor:pointer; margin-left:30px; display:block; margin-top:-410px;">
            <img style="zoom:0.7;" src="/nm/image/first_face/m_btn_re.png" alt="" onclick="location.reload();" />
          </div>
        </div>
      </div>
    </section>

    <!-- show(12) d 결과 -->
    <section id="lovetest" class="d">
      <div class="join-charge">
        <div style="background-image: url('/nm/image/first_face/m_result.png'); height:550px; background-repeat: no-repeat; background-size: contain; background-position:center;">
          <div style="text-align:center;">
            <p style="font-size:30px; color:#ff5193; padding-top:172px;">유머러스한 사람</p>
            <div style="padding-bottom:60px;"></div>
            <p style="font-size:17px;">활발한 말 솜씨로
              같이 있으면<br><br>
              주변인을 기쁘게 할것같은 스타일이에요!<br><br>
              밝고 유머러스한 성격하면<br><br>
              대표적으로 떠오르는 이미지!
            </p>
          </div>
          <div style="cursor:pointer; margin-left:30px; display:block; margin-top:-410px;">
            <img style="zoom:0.7;" src="/nm/image/first_face/m_btn_re.png" alt="" onclick="location.reload();" />
          </div>
        </div>
      </div>
    </section>
  </div>
  <!-- //컨텐츠 영역 -->
  <?
  require_once($_SERVER["DOCUMENT_ROOT"] . "/nm/common/footer.php");
  ?>
</body>

</html>
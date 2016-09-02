<?php
require_once "jssdk.php";
$jssdk = new JSSDK("wxd946ab56ca4d238a", "04409ec97b1c1d2013367ad771e86669");
$signPackage = $jssdk->GetSignPackage();
?>
<!DOCTYPE html>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5.0, minimun-scale=1.0, user-scalable=0">
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>荀辰龙的jssdk测试</title>
</head>
<body>
  <div>
    测试内容 <p>超级矿资源</p> 123
  </div>
</body>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
  /*
   * 注意：
   * 1. 所有的JS接口只能在公众号绑定的域名下调用，公众号开发者需要先登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”。
   * 2. 如果发现在 Android 不能分享自定义内容，请到官网下载最新的包覆盖安装，Android 自定义分享接口需升级至 6.0.2.58 版本及以上。
   * 3. 常见问题及完整 JS-SDK 文档地址：http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
   *
   * 开发中遇到问题详见文档“附录5-常见错误及解决办法”解决，如仍未能解决可通过以下渠道反馈：
   * 邮箱地址：weixin-open@qq.com
   * 邮件主题：【微信JS-SDK反馈】具体问题
   * 邮件内容说明：用简明的语言描述问题所在，并交代清楚遇到该问题的场景，可附上截屏图片，微信团队会尽快处理你的反馈。
   */
  wx.config({
    debug: true,
    appId: '<?php echo $signPackage["appId"];?>',
    timestamp: <?php echo $signPackage["timestamp"];?>,
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    jsApiList: [
      // 所有要调用的 API 都要加到这个列表中
      'onMenuShareTimeline','onMenuShareAppMessage'
    ]
  });
  wx.ready(function () {
    // 在这里调用 API

    // 分享到朋友圈
    wx.onMenuShareTimeline({

	    title: '将JSSDK测试分享到朋友圈', // 分享标题

	    link: 'https://www.baidu.com/', // 分享链接

	    imgUrl: 'https://ss0.bdstatic.com/5aV1bjqh_Q23odCf/static/superman/img/logo/bd_logo1_31bdc765.png', // 分享图标

	    success: function () { 

	        // 用户确认分享后执行的回调函数
	        alert('分享成功xcl');
	    },

	    cancel: function () { 

	        // 用户取消分享后执行的回调函数
	        alert('分享失败xcl');
	    }

	});

	//分享给朋友
	wx.onMenuShareAppMessage({

	    title: '超级矿资源APP', // 分享标题

	    desc: '物云信息科技有限公司', // 分享描述

	    link: 'http://www.kuaimei56.com/sample.php', // 分享链接

	    imgUrl: 'http://www.kuaimei56.com/copyright.png', // 分享图标

	    type: '', // 分享类型,music、video或link，不填默认为link

	    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空

	    success: function () { 

	        // 用户确认分享后执行的回调函数
	        alert('超级矿资源：你分享成功了！');
	    },

	    cancel: function () { 

	        // 用户取消分享后执行的回调函数
	        alert('超级矿资源：你咋不继续分享了涅？')
	    }

	});
  });
</script>
</html>

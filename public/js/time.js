// 経過病数
var iPassedSec = 0;

// 開始時刻(ms)をformに設定
function setStartMsec(id)
{
  document.getElementById(id).value = Date.now();
}

// 経過時間(ms)をformに設定
function setPassedMsec(id)
{
  document.getElementById(id).value = Date.now() - dStartMsec;
}

// 経過時間を更新
function updatePassedSec()
{
  // 表示は 99:59 まで
  if (iPassedSec > 5999) {
    iPassedSec = 5999;
  }
  var iSec = iPassedSec % 60;
  var iLowSec = iSec % 10;
  // 表示を更新
  document.getElementById('PassedTime').innerHTML = (iPassedSec - iSec) / 60 + ':' + (iSec - iLowSec) / 10 + iLowSec;
  iPassedSec++;
}

// 経過時間を秒数に変換
function passedMsecToSec(dPassedMsec)
{
  iPassedSec = parseInt(dPassedMsec / 1000);
}

// dStartMsecを経過時間として表示
// 引数は使用しないこと。
function dispTime()
{
  passedMsecToSec(dStartMsec);
  updatePassedSec();
}

// 次のタイマをセット
function setNextTimer()
{
  updatePassedSec();
  // その後は1秒毎に呼び出し
  setInterval(updatePassedSec, 1000);
}

// 1秒毎に表示を行うタイマをセット
// 引数は使用しないこと。
function setDispTimer()
{
  // 最初に表示
  dPassedMsec = Date.now() - dStartMsec;
  passedMsecToSec(dPassedMsec);
  updatePassedSec();
  // 1秒以下の時間を待って呼び出し
  setTimeout(setNextTimer, 1000 - parseInt(dPassedMsec) % 1000);
}

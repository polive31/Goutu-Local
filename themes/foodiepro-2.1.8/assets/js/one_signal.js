  window.OneSignal = window.OneSignal || [];
  
  /* In milliseconds, time to wait before prompting user. This time is relative to right after the user presses <ENTER> on the address bar and navigates to your page */
  var notificationPromptDelay = 60000;
  
  /* Why use .push? See: http://stackoverflow.com/a/38466780/555547 */
  window.OneSignal.push(function() {
    /* Use navigation timing to find out when the page actually loaded instead of using setTimeout() only which can be delayed by script execution */
    var navigationStart = window.performance.timing.navigationStart;

    /* Get current time */
    var timeNow = Date.now();

    /* Prompt the user if enough time has elapsed */
    setTimeout(promptAndSubscribeUser, Math.max(notificationPromptDelay - (timeNow - navigationStart), 0));
  });
  
  function promptAndSubscribeUser() {
    /* Want to trigger different permission messages? See: https://documentation.onesignal.com/docs/permission-requests#section-onesignal-permission-messages */
    window.OneSignal.isPushNotificationsEnabled(function(isEnabled) {
      if (!isEnabled) {        
        window.OneSignal.registerForPushNotifications();
      }
    });
  }
package com.pep.bettingtips;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.net.Uri;
import android.os.Build;
import android.os.VibrationEffect;
import android.os.Vibrator;
import android.view.HapticFeedbackConstants;
import android.view.View;
import android.view.WindowManager;
import android.webkit.JavascriptInterface;

import com.getcapacitor.JSObject;
import com.getcapacitor.Plugin;
import com.getcapacitor.PluginCall;
import com.getcapacitor.PluginMethod;
import com.getcapacitor.annotation.CapacitorPlugin;
import com.google.android.play.core.review.ReviewInfo;
import com.google.android.play.core.review.ReviewManager;
import com.google.android.play.core.review.ReviewManagerFactory;
import com.google.android.gms.tasks.Task;

@CapacitorPlugin(name = "AppNative")
public class AppNativePlugin extends Plugin {

    // --- 1. FLAG_SECURE (Screen recording & screenshot prevention) ---
    @PluginMethod
    public void setSecureFlag(final PluginCall call) {
        final boolean enable = call.getBoolean("enable", true);
        final Activity activity = getActivity();
        if (activity == null) {
            call.reject("Activity is null");
            return;
        }

        activity.runOnUiThread(() -> {
            try {
                if (enable) {
                    activity.getWindow().setFlags(
                        WindowManager.LayoutParams.FLAG_SECURE,
                        WindowManager.LayoutParams.FLAG_SECURE
                    );
                } else {
                    activity.getWindow().clearFlags(
                        WindowManager.LayoutParams.FLAG_SECURE
                    );
                }
                JSObject ret = new JSObject();
                ret.put("secure", enable);
                call.resolve(ret);
            } catch (Exception e) {
                call.reject("Failed to set secure flag: " + e.getMessage());
            }
        });
    }

    // --- 2. KEEP AWAKE (Prevent screen sleep on tips page) ---
    @PluginMethod
    public void setKeepAwake(final PluginCall call) {
        final boolean enable = call.getBoolean("enable", true);
        final Activity activity = getActivity();
        if (activity == null) {
            call.reject("Activity is null");
            return;
        }

        activity.runOnUiThread(() -> {
            try {
                if (enable) {
                    activity.getWindow().addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);
                } else {
                    activity.getWindow().clearFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);
                }
                JSObject ret = new JSObject();
                ret.put("keepAwake", enable);
                call.resolve(ret);
            } catch (Exception e) {
                call.reject("Failed to set keep awake: " + e.getMessage());
            }
        });
    }

    // --- 3. HAPTIC FEEDBACK (Vibration on click / tabs / pull-to-refresh) ---
    @PluginMethod
    public void triggerHaptic(final PluginCall call) {
        final String style = call.getString("style", "light");
        final Activity activity = getActivity();
        if (activity != null) {
            performHaptic(activity, style);
        }
        call.resolve();
    }

    public static void performHaptic(Activity activity, String style) {
        if (activity == null) return;
        try {
            View view = activity.getWindow().getDecorView();
            if ("heavy".equalsIgnoreCase(style)) {
                view.performHapticFeedback(HapticFeedbackConstants.LONG_PRESS);
            } else if ("medium".equalsIgnoreCase(style)) {
                view.performHapticFeedback(HapticFeedbackConstants.VIRTUAL_KEY);
            } else {
                view.performHapticFeedback(HapticFeedbackConstants.KEYBOARD_TAP);
            }

            Vibrator vibrator = (Vibrator) activity.getSystemService(Context.VIBRATOR_SERVICE);
            if (vibrator != null && vibrator.hasVibrator()) {
                long duration = 15;
                if ("medium".equalsIgnoreCase(style)) duration = 30;
                if ("heavy".equalsIgnoreCase(style)) duration = 60;

                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
                    vibrator.vibrate(VibrationEffect.createOneShot(duration, VibrationEffect.DEFAULT_AMPLITUDE));
                } else {
                    vibrator.vibrate(duration);
                }
            }
        } catch (Exception ignored) {}
    }

    // --- 4. IN-APP REVIEW (Google Play in-app review popup) ---
    @PluginMethod
    public void requestInAppReview(final PluginCall call) {
        final Activity activity = getActivity();
        if (activity == null) {
            call.reject("Activity is null");
            return;
        }

        try {
            final ReviewManager manager = ReviewManagerFactory.create(activity);
            Task<ReviewInfo> request = manager.requestReviewFlow();
            request.addOnCompleteListener(task -> {
                if (task.isSuccessful()) {
                    ReviewInfo reviewInfo = task.getResult();
                    Task<Void> flow = manager.launchReviewFlow(activity, reviewInfo);
                    flow.addOnCompleteListener(flowTask -> {
                        JSObject ret = new JSObject();
                        ret.put("success", true);
                        ret.put("type", "in_app_review_launched");
                        call.resolve(ret);
                    });
                } else {
                    // Fallback to direct Play Store intent
                    openPlayStore(activity);
                    JSObject ret = new JSObject();
                    ret.put("success", true);
                    ret.put("type", "play_store_fallback");
                    call.resolve(ret);
                }
            });
        } catch (Exception e) {
            openPlayStore(activity);
            JSObject ret = new JSObject();
            ret.put("success", false);
            ret.put("fallback", true);
            call.resolve(ret);
        }
    }

    private static void openPlayStore(Activity activity) {
        try {
            String packageName = activity.getPackageName();
            Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse("market://details?id=" + packageName));
            intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
            activity.startActivity(intent);
        } catch (Exception e) {
            try {
                String packageName = activity.getPackageName();
                Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse("https://play.google.com/store/apps/details?id=" + packageName));
                intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                activity.startActivity(intent);
            } catch (Exception ignored) {}
        }
    }

    // --- JAVASCRIPT INTERFACE FOR WEBVIEW CALLS ---
    public static class AndroidBridge {
        private final Activity activity;

        public AndroidBridge(Activity activity) {
            this.activity = activity;
        }

        @JavascriptInterface
        public void setSecureFlag(final boolean enable) {
            if (activity == null) return;
            activity.runOnUiThread(() -> {
                try {
                    if (enable) {
                        activity.getWindow().setFlags(WindowManager.LayoutParams.FLAG_SECURE, WindowManager.LayoutParams.FLAG_SECURE);
                    } else {
                        activity.getWindow().clearFlags(WindowManager.LayoutParams.FLAG_SECURE);
                    }
                } catch (Exception ignored) {}
            });
        }

        @JavascriptInterface
        public void setKeepAwake(final boolean enable) {
            if (activity == null) return;
            activity.runOnUiThread(() -> {
                try {
                    if (enable) {
                        activity.getWindow().addFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);
                    } else {
                        activity.getWindow().clearFlags(WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);
                    }
                } catch (Exception ignored) {}
            });
        }

        @JavascriptInterface
        public void triggerHaptic(final String style) {
            performHaptic(activity, style);
        }

        @JavascriptInterface
        public void requestInAppReview() {
            if (activity == null) return;
            activity.runOnUiThread(() -> {
                try {
                    ReviewManager manager = ReviewManagerFactory.create(activity);
                    Task<ReviewInfo> request = manager.requestReviewFlow();
                    request.addOnCompleteListener(task -> {
                        if (task.isSuccessful()) {
                            manager.launchReviewFlow(activity, task.getResult());
                        } else {
                            openPlayStore(activity);
                        }
                    });
                } catch (Exception e) {
                    openPlayStore(activity);
                }
            });
        }
    }
}

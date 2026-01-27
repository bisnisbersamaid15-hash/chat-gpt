package com.example.interactiontracker

import android.accessibilityservice.AccessibilityService
import android.graphics.Rect
import android.view.accessibility.AccessibilityEvent
import android.view.accessibility.AccessibilityNodeInfo
import org.json.JSONObject

class InteractionAccessibilityService : AccessibilityService() {
    override fun onAccessibilityEvent(event: AccessibilityEvent) {
        val source: AccessibilityNodeInfo? = event.source
        val bounds = Rect()
        source?.getBoundsInScreen(bounds)

        val payload = JSONObject().apply {
            put("device_id", WebSocketManager.DEVICE_ID)
            put("package_name", event.packageName?.toString() ?: "")
            put("event_type", AccessibilityEvent.eventTypeToString(event.eventType))
            put("text", event.text?.joinToString(" ") ?: "")
            put("content_description", event.contentDescription?.toString() ?: "")
            put("view_id", source?.viewIdResourceName)
            put("l", if (source == null) JSONObject.NULL else bounds.left)
            put("t", if (source == null) JSONObject.NULL else bounds.top)
            put("r", if (source == null) JSONObject.NULL else bounds.right)
            put("b", if (source == null) JSONObject.NULL else bounds.bottom)
            put("ts", System.currentTimeMillis())
        }

        WebSocketManager.sendEvent(payload)
    }

    override fun onInterrupt() {
        // No-op
    }
}

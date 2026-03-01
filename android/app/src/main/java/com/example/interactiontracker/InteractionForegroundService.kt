package com.example.interactiontracker

import android.app.Notification
import android.app.NotificationChannel
import android.app.NotificationManager
import android.app.Service
import android.content.Intent
import android.os.Build
import android.os.IBinder

class InteractionForegroundService : Service() {
    override fun onCreate() {
        super.onCreate()
        val channelId = "interaction_tracker"
        val manager = getSystemService(NotificationManager::class.java)
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            val channel = NotificationChannel(
                channelId,
                "Interaction Tracker",
                NotificationManager.IMPORTANCE_LOW
            )
            manager.createNotificationChannel(channel)
        }
        val notification = Notification.Builder(this, channelId)
            .setContentTitle("Interaction Tracker")
            .setContentText("Tracking UI interactions")
            .setSmallIcon(android.R.drawable.presence_online)
            .build()
        startForeground(1001, notification)
        WebSocketManager.connect(applicationContext)
    }

    override fun onStartCommand(intent: Intent?, flags: Int, startId: Int): Int {
        return START_STICKY
    }

    override fun onBind(intent: Intent?): IBinder? = null
}

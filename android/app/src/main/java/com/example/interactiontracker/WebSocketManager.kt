package com.example.interactiontracker

import android.content.Context
import android.util.Log
import okhttp3.OkHttpClient
import okhttp3.Request
import okhttp3.WebSocket
import okhttp3.WebSocketListener
import org.json.JSONObject
import java.util.concurrent.atomic.AtomicReference

object WebSocketManager {
    private const val TAG = "WebSocketManager"
    const val DEVICE_ID = "device-123"
    private const val API_KEY = "device-key"
    private const val BASE_URL = "ws://10.0.2.2:8000/ws/device"

    private val client = OkHttpClient()
    private var socket: WebSocket? = null
    val connectionState = AtomicReference("disconnected")

    fun connect(context: Context) {
        if (socket != null) return
        val request = Request.Builder()
            .url("$BASE_URL/$DEVICE_ID?api_key=$API_KEY")
            .build()
        socket = client.newWebSocket(request, object : WebSocketListener() {
            override fun onOpen(webSocket: WebSocket, response: okhttp3.Response) {
                connectionState.set("connected")
                Log.d(TAG, "WebSocket connected")
            }

            override fun onFailure(
                webSocket: WebSocket,
                t: Throwable,
                response: okhttp3.Response?
            ) {
                connectionState.set("disconnected")
                Log.e(TAG, "WebSocket error", t)
            }

            override fun onClosing(webSocket: WebSocket, code: Int, reason: String) {
                connectionState.set("closing")
                webSocket.close(code, reason)
            }

            override fun onClosed(webSocket: WebSocket, code: Int, reason: String) {
                connectionState.set("disconnected")
            }
        })
    }

    fun sendEvent(payload: JSONObject) {
        socket?.send(payload.toString())
    }
}

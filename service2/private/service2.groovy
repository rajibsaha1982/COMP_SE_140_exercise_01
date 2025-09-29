#!/usr/bin/env groovy
@Grab('com.sparkjava:spark-core:2.9.4')
@Grab('org.slf4j:slf4j-simple:2.0.7')

import static spark.Spark.*
import java.time.*

def storageUrl = 'http://storage:8000/log'
def volumeFile = '/vstorage'

def nowUTC() {
    Instant.now().toString()
}

def uptimeHours() {
    def uptimeSec = new File('/proc/uptime').text.split()[0].toDouble()
    
    String.format('%.2f', uptimeSec / 3600)
}

def freeMB() {
    new File('/').getFreeSpace() / (1024 * 1024) as int
}

def postToStorage(data) {
    def conn = new URL(storageUrl).openConnection()
    
    conn.setRequestMethod('POST')
    conn.setDoOutput(true)
    conn.setRequestProperty('Content-Type', 'text/plain')
    conn.getOutputStream().withWriter('UTF-8') { it << data }
    conn.getResponseCode()
}

port(8080)

get('/', { req, res ->
    try {
        def record = "${nowUTC()}: uptime ${uptimeHours()} hours, free disk in root: ${freeMB()} MBytes"
        
        try {
            postToStorage(record)
        } 
        catch (Exception ex) {
            println "Failed to post to storage: ${ex.message}"
        }

        try {
            new File(volumeFile).withWriterAppend { it << record + "\n" }
        } 
        catch (Exception ex) {
            println "Failed to write to volume: ${e.message}"
        }

        res.type('text/plain')
        return record
    } 
    catch (Exception ex) {
        res.status(500)
        return "Error: ${ex.message}"
    }
})

#include <WiFi.h>
#include <HTTPClient.h>
#include <Arduino_JSON.h>
#include "esp_sleep.h"

const char* ssid = "DELSA52BD9C"; 
const char* password = "2283524938";
const char* serverName_main = "https://digi-ai.ir/";
const char* serverName_feedback = "https://digi-ai.ir/feedback.php";


const uint64_t TIME_TO_SLEEP = 10800 * 1000000ULL ;//second
const uint16_t MAX_DELAY= 2000;// ms
const uint16_t MID_DELAY =1000;
const uint16_t MIN_DELAY= 500;
const uint8_t RELAY_POMP = 25;
const uint8_t RELAY_SENSOR = 32;
const uint8_t ADC_PIN = 34;  
const uint8_t LED = 2;
JSONVar zero=0;
JSONVar one=1;
String input ;
uint16_t get_ADC(uint8_t sensor_pin);
void wifi_connect(uint8_t repeat);
String send_request(int sensor,String key,String servername);
void watering(uint32_t uptime_s);
void setup() 
{
  delay(MAX_DELAY);
  pinMode(LED, OUTPUT);
  pinMode(RELAY_POMP, OUTPUT);
  pinMode(RELAY_SENSOR, OUTPUT);
  digitalWrite(RELAY_POMP,HIGH);//make pomp off
  digitalWrite(RELAY_SENSOR,HIGH);//make sensor off
 
  Serial.begin(115200);//1
 
  wifi_connect(2);

  if(WiFi.status() == WL_CONNECTED)
  {
   uint16_t sensor=5000;
   digitalWrite(RELAY_SENSOR,LOW);//make sensor on
    delay(MIN_DELAY);
    sensor=get_ADC(ADC_PIN);
    Serial.println(sensor);//4
    delay(MIN_DELAY);

   input=send_request(sensor,"humidity",serverName_main);

   }
  else//if not connected to wifi
  {
    Serial.println("in not connected");//4
    WiFi.disconnect();
    delay(MIN_DELAY);
   esp_deep_sleep(TIME_TO_SLEEP );
  }
}

void loop() 
{
  if(WiFi.status() == WL_CONNECTED)
  {
  JSONVar recive =JSON.parse(input);
  JSONVar keys = recive.keys();
  JSONVar value=recive[keys[0]];
  if(value == zero)
    {
    Serial.println("start value==0");//4
    WiFi.disconnect();
    digitalWrite(RELAY_SENSOR, HIGH);//make sensor off
    delay(MID_DELAY);
    esp_deep_sleep(TIME_TO_SLEEP);
    }
    if(value == one)
    { 
      String uptime_String;
      uint32_t uptime_int=0;
      uint16_t feedback=5000;
      JSONVar uptime_js;
      uint8_t len;
  
      Serial.println("start value==1");//5
      Serial.println(recive[keys[1]]);//6
      digitalWrite(RELAY_SENSOR, HIGH);//make sensor off
      uptime_js=recive[keys[1]];
      uptime_String=JSON.stringify(uptime_js);
      len=uptime_String.length(); 
      uptime_String=uptime_String.substring(1,(len-1));
      Serial.println(uptime_String);//6
      uptime_int=uptime_String.toInt();
      Serial.println(uptime_int);//6 
      watering(uptime_int);
      delay(MAX_DELAY * 20);//پایین رفتن اب
      digitalWrite(RELAY_SENSOR,LOW);//make sensor on
      delay(MIN_DELAY);
      feedback=get_ADC(ADC_PIN);
      delay(MIN_DELAY);
      input=send_request(feedback,"feedback",serverName_feedback);
    }
   }
}
void wifi_connect(uint8_t repeat)
{ 
  Serial.println("in wifi connect and repeat="+String(repeat));
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid,password);
  delay(MID_DELAY);

  for (int i=0; i<100; i++)
  {
    if(WiFi.status() != WL_CONNECTED)
    {
    digitalWrite(LED,HIGH);
    delay(100);
    digitalWrite(LED,LOW);
    delay(100);
    }
    else
    {
     break;
    }
  }
  if(WiFi.status() != WL_CONNECTED && 0<repeat)
  {
    repeat--;
    delay(MAX_DELAY*60);
    wifi_connect(repeat);
  }
 }
String send_request(int sensor,String key,String servername)
{
  Serial.println("in send request");//1
    HTTPClient http;
    http.begin(servername);
    delay(MID_DELAY);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
    String httpRequestData = key +"="+String(sensor);
  //  Serial.println("data send="+ httpRequestData);//2
    int httpResponseCode = 0;
    for (int i=0; i<10; i++) 
    {
      if(httpResponseCode<200)
         {
          httpResponseCode=http.POST(httpRequestData);
          Serial.println(httpResponseCode);//3
          delay(MAX_DELAY*10);
         }
         else {
         break;
         }
    }
    if(httpResponseCode==200)
    {
    delay(MAX_DELAY);
    String input=http.getString();
    delay(MAX_DELAY);
    Serial.println("data come=");//3
    Serial.println(input);//2
    http.end();
    return input;
    }
    else//if cannot send request
    {
    String input = "{\"status\":0}";
    http.end();
    return input;
    }
}
uint16_t get_ADC(uint8_t sensor_pin)
{
  uint16_t value=0;
  for(int i=0;i<10;i++)
  {
    value += analogRead(sensor_pin);
  }
  return value/10;
}
void watering(uint32_t uptime_s)
{
//s=second m=minute 
Serial.println("in watering");//6
uint16_t uptime_m = uptime_s/60;
for(int i=0;i<=uptime_m;i++)
{
  digitalWrite(RELAY_POMP,LOW);//make pomp on
  delay(60000);//uptime=1m
  digitalWrite(RELAY_POMP,HIGH);//make pomp off
  delay(30000);//rest time=0.5m
}
}

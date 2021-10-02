package Control;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.sql.*;
import java.time.Instant;
import java.util.Scanner;
import java.util.ArrayList;
import java.sql.Time;
import java.time.LocalTime; 

public class Control {
   
   //Database Properties
   static final String DB_URL ="jdbc:mysql://localhost:3306/theme_park";
   static final String DB_DRV ="com.mysql.jdbc.Driver";
   static final String DB_USER = "root";
   static final String DB_PASSWD = "tesis2018";

   public static void main(String[] args){
       LocalTime right_now = LocalTime.now();
       LocalTime closingTime = LocalTime.of(21, 0, 0, 0);
       
       //Realizar la tarea hasta la hora de cerrar
       while(right_now.isBefore(closingTime)){
            updateDatabase();
            right_now = LocalTime.now();
            delete_old_fastapasess();
            System.out.println("La hora actual: " + right_now);
            System.out.println("La hora de cerrar: " + closingTime);
       }
       
       //Limpiar base de datos al terminar el día
       after_hours();
       
   }
   
   //Metodo para actualizar la base de datos
   public static void updateDatabase(){
      Scanner in = new Scanner(System.in);
      Connection connection = null;
      Statement statement = null;
      ResultSet resultSet = null;

      try{
         connection=DriverManager.getConnection(DB_URL,DB_USER,DB_PASSWD);
         statement=connection.createStatement();
         resultSet=statement.executeQuery("SELECT * FROM guests");
         while(resultSet.next()){ //Por cada guest
            String id = resultSet.getString(1); //ID
            String q1 = resultSet.getString(2); //Q1
            String q2 = resultSet.getString(3); //Q2
            String q3 = resultSet.getString(4); //Q3
            if(!(q1==null && q2==null && q3==null)){ //Si el usuario está en alguna fila agarro sus tiempos
                Time t1 = resultSet.getTime(5); //T1
                Time t2 = resultSet.getTime(6); //T2
                Time t3 = resultSet.getTime(7); //T3
                checkQueueAndTime(id,q1,t1,connection,1); //Reviso si tiene un tiempo asignado a la fila donde está
                checkQueueAndTime(id,q2,t2,connection,2);
                checkQueueAndTime(id,q3,t3,connection,3);
            }  
         }
         //Actualizar la tabla hora en base de datos con la hora actual
         update_hour(connection);
      }catch(SQLException ex){
      }finally{
         try {
            resultSet.close();
            statement.close();
            connection.close();
         } catch (SQLException ex) {
         }
      }
    }
   
    //Método para realizar actualizaciones en tiempos de espera y asignaciones de turnos
    public static void checkQueueAndTime(String id, String queue, Time time, Connection connection, int row ){
        Statement statementTwo = null;
        ResultSet resultSetTwo = null;
        if(queue!=null && time==null){
           try{
            statementTwo=connection.createStatement();
            resultSetTwo=statementTwo.executeQuery("SELECT Name FROM rides_table_names WHERE IDRide="+queue);
            resultSetTwo.next();
            String rideName = resultSetTwo.getString(1);
            rideWaitTimeUpdate(queue,connection); 
            setGuestTurn(queue,connection,id,rideName,row); 
           }catch(SQLException ex){
               System.out.println(ex);
           }finally{
                try {
                    statementTwo.close();
                } catch (SQLException ex) {
                }
            }
        }
       else{
           if(queue!=null && time!=null){ 
               exitRow(id,queue,time,connection,row);
            }
        }  
    }
    
     //En funcion del tiempo de espera, le da al usuario la hora de retorno al juego
    public static void setGuestTurn(String queue, Connection connection, String id, String rideName, int row){
        Statement statementFour = null;
        ResultSet resultSetFour = null;
        try{
            statementFour=connection.createStatement();     
            resultSetFour=statementFour.executeQuery("SELECT WaitTime FROM rides WHERE IDRide="+queue);
            resultSetFour.next();
            int waitTime = resultSetFour.getInt(1);
            LocalTime addTimes = LocalTime.now().plusMinutes(waitTime).withNano(0);
            Time returnTime = java.sql.Time.valueOf(addTimes);
            String parsedRetTime = returnTime.toString();
            parsedRetTime = parsedRetTime.replaceAll(":", "");
            statementFour.executeUpdate("INSERT INTO "+rideName+" (IDGuest, Time) VALUES ("+id+","+parsedRetTime+")");
            statementFour.executeUpdate("UPDATE guests SET T" + row + "=" + parsedRetTime + " WHERE ID=" +id);
            statementFour.executeUpdate("UPDATE rides SET Booked=Booked+1 WHERE IDRide=" +queue); 
        }catch(SQLException ex){
               System.out.println(ex);
        }finally{
            try {
                statementFour.close();
            } catch (SQLException ex) {
            }
        }
    }
    
    //Método que calculo el tiempo de espera para cada ride
    public static void rideWaitTimeUpdate(String queue, Connection connection){
        Statement statementThree = null;
        ResultSet resultSetThree = null;
        try{
            statementThree=connection.createStatement();
            resultSetThree=statementThree.executeQuery("SELECT Queuing, NextCartIn, Capacity FROM rides WHERE IDRide="+queue);
            resultSetThree.next();
            int queuing = resultSetThree.getInt(1);
            double nextCartIn = resultSetThree.getDouble(2);
            int capacity = resultSetThree.getInt(3);
            double loadPeopleAndUnload=(0.05*queuing);
            double cart=(queuing/capacity)*nextCartIn;
            double waitTimeDouble = loadPeopleAndUnload + cart;
            int waitTime = (int)(Math.floor(waitTimeDouble));
            queuing++;        
            statementThree.executeUpdate("UPDATE rides SET Queuing="+queuing+ ", WaitTime=" + waitTime + " WHERE IDRide="+queue);
        }catch(SQLException ex){ 
               System.out.println(ex);
        }finally{
            try {
                statementThree.close();
            } catch (SQLException ex) {
            }
        }
    }
    
    //Método para sacar de una fila al usuario cuando venció su turno
    public static void exitRow(String id, String queue, Time time, Connection connection, int row){
        Statement statementTwo = null;
        ResultSet resultSetTwo = null;
        int tiempo_entre_tandas = 0;
        tiempo_entre_tandas = tiempo_gracia_molinete(connection, queue);
        System.out.println("Tiempo entre tandas: " + tiempo_entre_tandas);
        LocalTime rightNow = LocalTime.now();
        System.out.println((time.toLocalTime().plusMinutes(tiempo_entre_tandas)));
        if(rightNow.isAfter(time.toLocalTime().plusMinutes(tiempo_entre_tandas))){
            try{                
                statementTwo=connection.createStatement();
                statementTwo.executeUpdate("UPDATE guests SET Q"+ row +"=" + null +", T" + row + "=" + null + " WHERE ID=" +id);
                resultSetTwo=statementTwo.executeQuery("SELECT Queuing FROM rides WHERE IDRide="+queue);
                resultSetTwo.next();
                int queuing = resultSetTwo.getInt(1);
                queuing--;                 
                statementTwo.executeUpdate("UPDATE rides SET Queuing="+queuing+" WHERE IDRide="+queue);
                resultSetTwo=statementTwo.executeQuery("SELECT Name FROM rides_table_names WHERE IDRide="+queue);
                resultSetTwo.next();
                String rideName = resultSetTwo.getString(1);
                System.out.println(rideName+id);
                statementTwo.executeUpdate("DELETE FROM "+rideName+" WHERE IDGuest="+id);
            }catch(SQLException ex){
                System.out.println(ex);
                }finally{
                 try {
                     statementTwo.close();
                 }catch (SQLException ex) {
                 }
                }
        }
    } 
       
    //Método para actualizar la hora en base de datos
    public static void update_hour(Connection connection){
        Statement statement = null;
        LocalTime rightNow = LocalTime.now();
        Time right_Now = java.sql.Time.valueOf(rightNow);
        String parsedTime = right_Now.toString();
        try{ 
            statement=connection.createStatement();
            parsedTime = parsedTime.replaceAll(":", "");
            System.out.println("UPDATE hora SET T0="+ parsedTime +" WHERE ID=0");
            statement.executeUpdate("UPDATE hora SET T0="+ parsedTime +" WHERE ID=0"); 
        }catch(SQLException ex){
            System.out.println(ex);
            }finally{
                 try {
                     statement.close();
                 }catch (SQLException ex) {
                 }
            }
    }
    
    //Método para limpiar la tabla guests de la base de datos
    public static void after_hours(){
         Connection connection = null;
         Statement statement = null;
         
         try{
            connection=DriverManager.getConnection(DB_URL,DB_USER,DB_PASSWD);
            statement=connection.createStatement();
            statement.executeUpdate("DELETE FROM guests");
            
         }catch(SQLException ex){
              System.out.println(ex);
         }finally{
            try {
                statement.close();
                connection.close();
            }catch (SQLException ex) {
            }
         }
    }
    
     public static void delete_old_fastapasess(){
         Connection connection = null;
         Statement statement = null;
         LocalTime rightNow = LocalTime.now();
         LocalTime max_fastpass = rightNow.plusMinutes(15);
         Time max_fastpass_time = java.sql.Time.valueOf(max_fastpass);
         String parsedTime = max_fastpass_time.toString();
         parsedTime = parsedTime.replaceAll(":", "");
         try{
            connection=DriverManager.getConnection(DB_URL,DB_USER,DB_PASSWD);
            statement=connection.createStatement();
            statement.executeUpdate("DELETE FROM fastpass WHERE Time_fastpass< "+parsedTime);
         }catch(SQLException ex){
              System.out.println(ex);
         }finally{
            try {
                statement.close();
                connection.close();
            }catch (SQLException ex) {
            }
         }
    }
     
     public static int tiempo_gracia_molinete(Connection connection, String queue ){
         Statement statement = null;
         ResultSet resultSet = null;
         int entre_tandas=0;
         try{
            statement=connection.createStatement();
            resultSet=statement.executeQuery("SELECT BetweenTandas FROM rides WHERE IDRide="+queue);
            resultSet.next();
            entre_tandas = resultSet.getInt(1);
         }catch(SQLException ex){
              System.out.println(ex);
         }finally{
            try {
                statement.close();
            }catch (SQLException ex) {
            }
         }
        return entre_tandas;
     }
    
    
}
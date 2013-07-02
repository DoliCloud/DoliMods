package com.saasplex.rm.utils

import com.saasplex.rm.*
import com.saasplex.rm.adapters.*


public class DolibarrScript extends ScriptContext {

    def installerUrl
    def dbScriptUrl
    def cfgFileUrl
    def applicationUniqueKey
    def cfgFileName = "htdocs/conf/conf.php"
    def appName = "dolibarr"

    def install() {
        installerUrl = variables["installerUrl"]
        dbScriptUrl = variables["dbScriptUrl"]
        cfgFileUrl = variables["cfgFileUrl"]
        
	applicationUniqueKey = randomString(32)
	setVar("applicationUniqueKey", applicationUniqueKey)	
        
        def domainName = getVar("domainName")
        if(!domainName) throw new Exception("Missing required parameter domainName")
        
        //get the servers
        def mysql = new Mysql() //default constructor chooses a agent
        def agent = mysql.agent
        def apache = new Apache2(agent) // we wnat to have both things in the same box
        def os = new Linux(agent)
        def fs = new FileSystem(agent)
        
        //get a unique identifier and a server
        String uid = randomString(15)
        def db
        def ip
        def app
        def appPath
        
        // Set the url of the resource
        resource.description = "dolibarr"
        resource.url = "https://" + domainName
 
        ip = agent.getIp()
        
        // Building the DB
        try {
            db = new DataBase() //this represents a database with its users and its recipe for creating tables,etc
            /**
              * Truncating the database name if it exceeds 40 characters. 
              * 
              * MySQL has limitations for Database/Table name length to 65 
              * bytes.
              */
             def databaseName = uid+'_'+appName
             if (databaseName.length() > 40) {
                 databaseName = databaseName.substring(0,40)
             }
             
             db.setName(databaseName.replaceAll(/\W/, "_"))
             db.setScript(dbScriptUrl)
             def dbUsername = "ebw" + randomString(7)
             def dbPassword = randomString(10)
             db.addUser(dbUsername, dbPassword) //mysql username limited to 16 chars. will truncate and delete/create so careful
             mysql.createDataBase(db)
             
             //set the variables needed for replacing values in the cfg template
             readVars(db)

             // Building the web app
             try {
                 def osUsername = "ebw" + randomString(7)
                 setParameter("osUsername", osUsername)
                 variables.put("osUsername", osUsername)
                 
                 def osPassword = randomString(10)
                 setParameter("osPassword", osPassword)
                 variables.put("osPassword", osPassword)
                 
                 def osGroupname = "${osUsername}Group"
                 setParameter("osGroupname", osGroupname)
                 variables.put("osGroupname", osGroupname)
                 
                 os.createGroup(osGroupname)
                 os.createUser(osUsername, osPassword, osGroupname)

                 appPath = "/home/jail/home/$osUsername/$uid"
                 
                 app = new PhpApp() // represents a web app to be deployed on a web server,subclass of WebApp
                 app.server = apache //we asing the appserver so when reading vars the Server based variables will be there
                 app.name = appName
                 app.domains = [domainName]
                 app.sourceUrl = installerUrl
                 app.path = appPath
                 app.logName = "osaas/dolibarr/$uid" 
                 app.addConfigFile(cfgFileName, cfgFileUrl)
                 app.setHttpMode(app.HTTPS_ONLY)
                 //app.setHttpMode(app.HTTP_HTTPS)
                 
                 // set the variables needed for replacing values in the cfg template,
                 //  and sets parameters associated with this resource
                 readVars(app)
                 
                 app.variables = getVariables()
                 
                 try {
                     apache.createWebApp(app)
                     fs.chown(appPath, "$osUsername:$osGroupname")
                     fs.chmod(appPath, '700')
                     fs.chmod(appPath+'/htdocs/conf/conf.php', '400')
                 } catch (e) {
                     log.error("Create Web Failed. So rolling back")
                     apache.removeApp(app)
                     throw e
                 }
            } catch(e) {
                    log.error("Rolling Back Unix user creation")
                    os.deleteUser(getParameter("osUsername"))
                    os.deleteGroup(getParameter("osGroupname"))
                    throw e
            }
            
        } catch(e) {
                log.error("Rolling Back DB creation")
                mysql.dropDataBase(db)
                log.error("DB Rolled Back")
                throw e
        }
    }

    def uninstall() {
        def server = getAgent(["ip" : getParameter("webServer")])
        def apache2 = new Apache2(server)
        def mysql = new Mysql(server)
        def os = new Linux(server)
        def domainName = getParameter("webAppDomain")
        def app = new PhpApp()
        app.name = appName
        app.domains = [domainName]
        app.path = getParameter("webAppPath")
        app.setHttpMode(getParameter("webAppHttpMode"))
        app.server = apache2
        apache2.removeApp(app)
        mysql.dropDataBase(getParameter("dbName"))
        mysql.deleteUser(getParameter("dbUser"))
        os.deleteUser(getParameter("osUsername"))
        os.deleteGroup(getParameter("osGroupname"))
        setStatus("uninstalled")
    }

}

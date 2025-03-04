<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
    <form method = "post" action = "process-form.php" >
        <h4>USER INFORMATION</h4>
    First Name:  <input type="text" id = "first_name" name="first_name" required> <br>
    Last Name:  <input type="text" id = last_name name="last_name" required> <br>
    Email:  <input type="email" id = email name="email" required><br>
    Title:<input type="text" id = "title" name="title" required><br>
    NetID:<input type="text" id = "newid" name="netid" required> <br>
    


    
    <br>



    
    <hr>
    <h4>SUPERVISOR INFORMATION</h4>
    Supervisor:  <input id = "sup_name" name="sup_name" required><br>
    
    Supervisor's Email:  <input id = "sup_email" name="sup_email" type="email" required><!---->
        <hr>
            <p>
                <b>REQUEST</b>


<input type="radio" id="new" name="request" value="new"> <label for="new">New Employee</label>

<input type="radio" id="samePosNewRole" name="request" value="samePosNewRole"> <label for="samePosNewRole">Same Position-New Role</label>

<input type="radio" id="transfer" name="request" value="transfer"> <label for="transfer">Transferring</label>

<input type="radio" id="removeAllAcces" name="request" value="removeAllAcces"> <label for="removeAllAcces">Remove All Access</label> 				
            </p>
        <hr>

        <p>
            <b>EMPLOYEE</b>

<input type="radio" id="staff" name="employee" value="staff"> <label for="staff">Staff</label>
<input type="radio" id="faculty" name="employee" value="faculty"> <label for="faculty">Faculty</label>
<input type="radio" id="student_worker" name="employee" value="student_worker"> <label for="student_worker">Student Worker</label>
        </p>
        <hr>

        <input type="submit" value="Submit">

    </form>

</body>
</html>


            <!-- 
  <table>
            <tr>
                <th>IT System</th>
                <th>Role</th>
                <th>Justification for Access Requested</th>
                <th>Action</th>
                <th>Access Grantor</th>
            </tr>
            <tr>
                <td>START(EMS)</td>
                <td>
                    
                    <select name="START" id="START">
<option value="admin"></option>  Add admin 
<option value="supervisor">Supervisor</option>
<option value="advisor">Student Adivsor</option>
<option value="informational">Informational</option>
</select>

                </td>


                <td>
                    <textarea></textarea>
                </td>
                <td>
                    <select>
                        <option>Add</option>
                        <option>Delete</option>
                    </select>
                </td>
                <td><button>Sign</button>&nbsp;<button>Regect</button></td>
            </tr>

            <tr>
                <td>GradTracker</td>
                <td>
                    
                    <select name="GradTracker" id="GradTracker">
<option value="gt_none" default></option>
<option value="gt_supervisor">Supervisor</option>
<option value="gt_certifier">Certifier</option>
<option value="gt_deptCertifier">Department Certifier</option>
<option value="gt_registrar">Registrar</option>
<option value="gt_informational">Informational</option>
<option value="gt_UTEP">Urban Teaching and Educational (UTEP)</option>
<option value="gt_BAMA">BA/MA</option>
<option value="gt_honors">Honors College</option>
</select>

                </td>

                
                <td>
                    <textarea></textarea>
                </td>
                <td>
                    <select>
                        <option>Add</option>
                        <option>Delete</option>
                    </select>
                </td>
                <td><button>Sign</button>&nbsp;<button>Regect</button></td>
            </tr>

<tr>
                <td>Junior Tracker</td>
                <td>
                    
                    <select name="jTracker" id="jTracker">
<option value="jt_none" default></option>
<option value="jt_supervisor">Supervisor</option>
<option value="jt_certifier">Certifier</option>
<option value="jt_deptCertifier">Department Certifier</option>
<option value="jt_registrar">Registrar</option>
<option value="jt_informational">Informational</option>
<option value="jt_UTEP">Urban Teaching and Educational (UTEP)</option>
<option value="jt_BAMA">BA/MA</option>
<option value="jt_honors">Honors College</option>
</select>

                </td>

                
                <td>
                    <textarea></textarea>
                </td>
                <td>
                    <select>
                        <option>Add</option>
                        <option>Delete</option>
                    </select>
                </td>
                <td><button>Sign</button>&nbsp;<button>Regect</button></td>
            </tr>



<tr>
                <td>AMS(PTL)</td>
                <td>
                    
                    <select name="GradTracker" id="GradTracker">
<option value="gt_none" default></option>
<option value="gt_supervisor">Supervisor</option>
<option value="gt_certifier">Certifier</option>
<option value="gt_deptCertifier">Department Certifier</option>
<option value="gt_registrar">Registrar</option>
<option value="gt_informational">Informational</option>
<option value="gt_UTEP">Urban Teaching and Educational (UTEP)</option>
<option value="gt_BAMA">BA/MA</option>
<option value="gt_honors">Honors College</option>
</select>

                </td>

                
                <td>
                    <textarea></textarea>
                </td>
                <td>
                    <select>
                        <option>Add</option>
                        <option>Delete</option>
                    </select>
                </td>
                <td><button>Sign</button>&nbsp;<button>Regect</button></td>
            </tr>


        <tr>
                <td>Faculty Integration</td>
                <td>
                    
                    <select name="GradTracker" id="GradTracker">
<option value="gt_none" default></option>
<option value="gt_supervisor">Supervisor</option>
<option value="gt_certifier">Certifier</option>
<option value="gt_deptCertifier">Department Certifier</option>
<option value="gt_registrar">Registrar</option>
<option value="gt_informational">Informational</option>
<option value="gt_UTEP">Urban Teaching and Educational (UTEP)</option>
<option value="gt_BAMA">BA/MA</option>
<option value="gt_honors">Honors College</option>
</select>

                </td>

                
                <td>
                    <textarea></textarea>
                </td>
                <td>
                    <select>
                        <option>Add</option>
                        <option>Delete</option>
                    </select>
                </td>
                <td><button>Sign</button>&nbsp;<button>Regect</button></td>
            </tr>
<tr>
                <td>SASN Website</td>
                <td>
                    
                    <select name="GradTracker" id="GradTracker">
<option value="gt_none" default></option>
<option value="gt_supervisor">Supervisor</option>
<option value="gt_certifier">Certifier</option>
<option value="gt_deptCertifier">Department Certifier</option>
<option value="gt_registrar">Registrar</option>
<option value="gt_informational">Informational</option>
<option value="gt_UTEP">Urban Teaching and Educational (UTEP)</option>
<option value="gt_BAMA">BA/MA</option>
<option value="gt_honors">Honors College</option>
</select>

                </td>

                
                <td>
                    <textarea></textarea>
                </td>
                <td>
                    <select>
                        <option>Add</option>
                        <option>Delete</option>
                    </select>
                </td>
                <td><button>Sign</button>&nbsp;<button>Regect</button></td>
            </tr>
<tr>
                <td>Kronos</td>
                <td>
                    
                    <select name="GradTracker" id="GradTracker">
<option value="gt_none" default></option>
<option value="gt_supervisor">Supervisor</option>
<option value="gt_certifier">Certifier</option>
<option value="gt_deptCertifier">Department Certifier</option>
<option value="gt_registrar">Registrar</option>
<option value="gt_informational">Informational</option>
<option value="gt_UTEP">Urban Teaching and Educational (UTEP)</option>
<option value="gt_BAMA">BA/MA</option>
<option value="gt_honors">Honors College</option>
</select>

                </td>

                
                <td>
                    <textarea></textarea>
                </td>
                <td>
                    <select>
                        <option>Add</option>
                        <option>Delete</option>
                    </select>
                </td>
                <td><button>Sign</button>&nbsp;<button>Regect</button></td>
            </tr>
        </table>
<hr>

        <hr>
        <p>
            ACCESS GRANTOR COMMENTS<br>
            Name:<input><br>
            Text: <input type="texy">
        </p>
        <hr>

        <hr>

        <p>
            <h3>APPROVAL</h3><br>
            <b>Supervisor</b>: By submitting this form, I certify that the user requires access to data within the above system(s) to
perform their job duties. I understand that it is my obligation to ensure that adequate training is provided to the user
in compliance with state and federal laws, and University policies governing access to information contained in
employee, applicant, and student records. I also certify that the person requesting access has read the "School of
Arts and Sciences Confidentiality Agreement
        </p>

        <p>
            Name: <input>
            Signature:<input>
            Date:<input>

        </p>
        <hr>
        <center> 
            <h3>
                SASN - Office of the Dean Approval
            </h3>
        </center>
        <p>
            <b>SASN Dean Approval</b>
            Name: <input>
            Signature: <input>
            Date: <input>
        </p>
        <p>
            <b>SASN IT Approval</b>
            Name: <input>
            Signature: <input>
            Date: <input>
        </p>

        <hr>

        <input type="submit">

        
    </form>
</body>
</html>

-->
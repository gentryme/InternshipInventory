<?php

  /**
   * Student
   * 
   * Represents the student that is participating in an internship.
   *
   * @author Robert Bost <bostrt at tux dot appstate dot edu>
   */

PHPWS_Core::initModClass('intern', 'Model.php');
class Student extends Model
{
    public $banner;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $phone;
    public $email;
    public $grad_prog;
    public $ugrad_major;
    public $graduated;

    /**
     * @Override Model::getDb
     */
    public function getDb()
    {
        return new PHPWS_DB('intern_student');
    }

    /**
     * @Override Model::getCSV
     */
    public function getCSV()
    {
        $csv = array();
        
        $csv['Banner'] = $this->banner;
        $csv['Student First Name'] = $this->first_name;
        $csv['Student Middle Name'] = $this->middle_name;
        $csv['Student Last Name']  = $this->last_name;
        $csv['Student Phone'] = $this->phone;
        $csv['Student Email'] = $this->email;
        $csv['Graduate Program'] = $this->grad_prog;
        $csv['Undergraduate Major'] = $this->ugrad_major;
        $csv['Graduated'] = $this->graduated==1 ? 'Yes' : 'No';
        
        return $csv;
    }

    /**
     * Get the concatenated first name, middle name/initial, and last name.
     */
    public function getFullName()
    {
        $name = $this->first_name;
        // Middle name is not required. If no middle name as input then
        // this will not show the extra space for padding between middle and last name.
        $name .= isset($this->middle_name) ? $this->middle_name.' ' : null;
        $name .= $this->last_name;
        return $name;
    }

    /**
     * Get a Student object by their banner id.
     */
    public static function getStudentByBanner($bannerId){
        $db = self::getDb();
        $db->addWhere('banner', $bannerId);
        $student = new Student();

        if(!$db->loadObject($student)){
            return null;
        }else {
            return $student;
        }
    }
}

?>
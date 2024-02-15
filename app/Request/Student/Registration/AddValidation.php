<?php

namespace App\Http\Requests\Student\Registration;

use Illuminate\Foundation\Http\FormRequest;
use Session;
class AddValidation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if(Session::get('isCourseBatch')){
            return [
            // 'reg_no'                        => 'required  | max:15 | unique:students,reg_no',
            // 'reg_date'                      => 'required',
            'faculty'                       => 'required',
            'batch_id'                         => 'required',
            'first_name'                    => 'required | max:205',
            // 'date_of_birth'                 => 'required',
            // 'gender'                        => 'required',
            // 'nationality'                   => 'required | max:15',
            // 'address'                       => 'required | max:100',
            // 'state'                         => 'required | max:25',
            // 'country'                       => 'required | max:25',
            // 'temp_address'                  => 'required | max:100',
            // 'temp_state'                    => 'required | max:25',
            // 'temp_country'                  => 'required | max:25',
            'email'                         => 'max:100 | unique:students,email',
            'extra_info'                    => 'max:100',
            'home_phone'                    => 'max:15',
            'mobile_1'                      => 'max:15',
            'mobile_2'                      => 'max:15',
            'grandfather_first_name'        => 'max:150',
            'grandfather_middle_name'       => 'max:150',
            'grandfather_last_name'         => 'max:150',
            'father_first_name'             => 'max:150',
            'father_middle_name'            => 'max:150',
            'father_last_name'              => 'max:150',
            'father_eligibility'            => 'max:50',
            'father_occupation'             => 'max:50',
            'father_office'                 => 'max:100',
            'father_office_number'          => 'max:150',
            'father_residence_number'       => 'max:150',
            'father_mobile_1'               => 'max:15',
            'father_mobile_2'               => 'max:15',
            'father_email'                  => 'max:100',
            'mother_first_name'             => 'max:150',
            'mother_middle_name'            => 'max:150',
            'mother_last_name'              => 'max:150',
            'mother_eligibility'            => 'max:50',
            'mother_occupation'             => 'max:50',
            'mother_office'                 => 'max:100',
            'mother_office_number'          => 'max:15',
            'mother_residence_number'       => 'max:15',
            'mother_mobile_1'               => 'max:15',
            'mother_mobile_2'               => 'max:15',
            'mother_email'                  => 'max:150',

            'institution.*'                   => 'max:100',
            'board.*'                         => 'max:50',
            'pass_year.*'                     => 'max:4',
            'symbol_no.*'                     => 'max:15',
            'division_grade.*'                => 'max:10',
            'major_subjects.*'                => 'max:50',

            'student_main_image'            => 'mimes:jpeg,bmp,png',
            'father_main_image'             => 'mimes:jpeg,bmp,png',
            'mother_main_image'             => 'mimes:jpeg,bmp,png',
            'guardian_main_image'           => 'mimes:jpeg,bmp,png',
        ];

        }else{
            return [
            // 'reg_no'                        => 'required  | max:15 | unique:students,reg_no',
            // 'reg_date'                      => 'required',
            'faculty'                       => 'required',
            'semester' => 'required',
            'first_name'                    => 'required | max:205',
            // 'date_of_birth'                 => 'required',
            // 'gender'                        => 'required',
            // 'nationality'                   => 'required | max:15',
            // 'address'                       => 'required | max:100',
            // 'state'                         => 'required | max:25',
            // 'country'                       => 'required | max:25',
            // 'temp_address'                  => 'required | max:100',
            // 'temp_state'                    => 'required | max:25',
            // 'temp_country'                  => 'required | max:25',
            'email'                         => 'max:100 | unique:students,email',
            'extra_info'                    => 'max:100',
            'home_phone'                    => 'max:15',
            'mobile_1'                      => 'max:15',
            'mobile_2'                      => 'max:15',
            'grandfather_first_name'        => 'max:150',
            'grandfather_middle_name'       => 'max:150',
            'grandfather_last_name'         => 'max:150',
            'father_first_name'             => 'max:150',
            'father_middle_name'            => 'max:150',
            'father_last_name'              => 'max:150',
            'father_eligibility'            => 'max:50',
            'father_occupation'             => 'max:50',
            'father_office'                 => 'max:100',
            'father_office_number'          => 'max:150',
            'father_residence_number'       => 'max:150',
            'father_mobile_1'               => 'max:15',
            'father_mobile_2'               => 'max:15',
            'father_email'                  => 'max:100',
            'mother_first_name'             => 'max:150',
            'mother_middle_name'            => 'max:150',
            'mother_last_name'              => 'max:150',
            'mother_eligibility'            => 'max:50',
            'mother_occupation'             => 'max:50',
            'mother_office'                 => 'max:100',
            'mother_office_number'          => 'max:15',
            'mother_residence_number'       => 'max:15',
            'mother_mobile_1'               => 'max:15',
            'mother_mobile_2'               => 'max:15',
            'mother_email'                  => 'max:150',

            'institution.*'                   => 'max:100',
            'board.*'                         => 'max:50',
            'pass_year.*'                     => 'max:4',
            'symbol_no.*'                     => 'max:15',
            'division_grade.*'                => 'max:10',
            'major_subjects.*'                => 'max:50',

            'student_main_image'            => 'mimes:jpeg,bmp,png',
            'father_main_image'             => 'mimes:jpeg,bmp,png',
            'mother_main_image'             => 'mimes:jpeg,bmp,png',
            'guardian_main_image'           => 'mimes:jpeg,bmp,png',
        ];
           
        }
            
        

    }

    public function messages()
    {
        if(Session::get('isCourseBatch')){
            return [
            'reg_no.unique'                  => 'Enter Unique Reg.No.',
            'batch_id.required'     =>'Please Select Batch'

        ];   
        }else{
            return [
            'reg_no.unique'                  => 'Enter Unique Reg.No.',

        ];
        }
        
    }
}

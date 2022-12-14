<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contact;
class ContactController extends Controller
{
	private $rules = [
    		'name' => ['required', 'min:5'],
    		'company' => ['required'],
    		'email' => ['required', 'email'],
    		'photo' => ['mimes:jpg,jpeg,png,bmp'],
    	];
    private $upload_dir = 'public/uploads';

    public function __construct(){
    	$this->middleware('auth');
    	$this->upload_dir = base_path() . '/' . $this->upload_dir;
    }

    public function autocomplete(Request $request)
    {
    	if($request->ajax){
    		return Contact::select(['id', 'name as value'])->where(function($query) use ($request) {
    				
    				if( ($term = $request->get("term"))){

    					$keywords = '%' . $term . '%';
    					$query->orWhere("name", 'LIKE', $keywords);
    					$query->orWhere("company", 'LIKE', $keywords);
    					$query->orWhere("email", 'LIKE', $keywords);
    				}
    					})
    					->orderBy('name', 'asc')
    					->take(5)
    					->get();
    	}
    	
    }

    public function index(Request $request){

        // \DB::enableQueryLog();
        // listGroups($request->user()->id);
        // dd(\DB::getQueryLogs());

    	$contacts = Contact::where(function($query) use ($request) {
    		$query->where("user_id",$request->user()->id);
    				if($group_id=($request->get('group_id'))){
    					$query->where('group_id', $group_id);
    				}

    				if( ($term = $request->get("term"))){

    					$keywords = '%' . $term . '%';
    					$query->orWhere("name", 'LIKE', $keywords);
    					$query->orWhere("company", 'LIKE', $keywords);
    					$query->orWhere("email", 'LIKE', $keywords);
    				}
    					})
    					->paginate(10);

    	return view('contacts.index', compact('contacts'));
    }

    public function create()
    {
    	return view("contacts.create");
    }

    public function edit($id)
    {
    	$contact = Contact::findOrFail($id);  
        $this->authorize('modify', $contact);
    	return view("contacts.edit", compact('contact'));	
    }

    public function update ($id, Request $request)
    {
    	$this->validate($request, $this-> rules);
    	$contact = Contact::findOrFail($id);  
        $this->authorize('modify', $contact); 
    	$oldPhoto = $contact->photo;
    	
    	$data = $this->getRequest($request);
    	$contact->update($data);
    	if($oldPhoto !== $contact->photo){
    		$this->removePhoto($oldPhoto);
    	}
 
    	return redirect('contacts')->with('message', 'Contact Updated!');
    }

    private function getRequest(Request $request){

    	$data = $request->all();
    	if($request->hasFile('photo')){
    		$photo = $request->file('photo');
    		$fileName = $photo->getClientOriginalName();
    		$destination = $this->upload_dir;
    		$photo->move($destination,$fileName); 
    		$data['photo'] = $fileName;
    	}
    	
    	return $data;
    }

    public function store(Request $request)
    {
    	$this->validate($request, $this->rules);

    	$data = $this->getRequest($request);
    	$request->user()->contacts()->create($data);

    	return redirect('contacts')->with('message', 'Contact Saved!');
    }

    public function destroy($id){

    	$contact = Contact::findOrFail($id);  
        $this->authorize('modify', $contact);
    	$contact->delete();
    	$this->removePhoto($contact->photo);
    	return redirect('contacts')->with('message','Contact Deleted!');
    }

    private function removePhoto($photo)
    {
    	if( ! empty($photo)){
    		$file_path = $this->upload_dir . '/' . $photo;
    		if( file_exists($file_path) ) unlink($file_path);
    	}
    }
}

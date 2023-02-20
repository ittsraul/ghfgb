import { Component } from '@angular/core';
import { AuthService } from 'src/app/services/auth.service';
import { ApiRequestService } from "../../services/api-request.service";
import { User, contents } from './user.interface';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css']
})
export class ProfileComponent {

  constructor(public service: ApiRequestService, public AuthService : AuthService) { }

  public perfil: number = 1;
  public contents: User = contents;
  public id: any = localStorage.getItem('id');

  public nombre : string = "";
  public apellidos : string = "";
  public telefono : string = "";
  public email : string = "";

  public peticio() {
    this.service.getUser(this.id).subscribe(response => {
      this.contents = {
        name: response.name,
        surname: response.surname,
        Email: response.Email,
        phone: response.phone,
        events: response.events,
      };

      this.nombre = response.name;
      this.apellidos = response.surname;
      this.telefono = response.phone;
      this.email = response.Email
    });

  }

  ngOnInit() {
    this.perfil = 1;
    this.peticio();
    console.log(this.id);
  }

  public onClic() {
    this.perfil = 2;
  }

  public onSubmit() {
    this.service.updateUser(this.id, this.nombre, this.apellidos, this.email, this.telefono).subscribe(response => { 
      console.log(response);
    });
    this.perfil = 1;
    this.peticio();
  }

  public cerrarSesion() 
  {
    this.AuthService.logOut();
  }



}

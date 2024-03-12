## Ticketless api

## THIS IS JUST A CODE PREVIEW WITH API KEYS ETC. REMOVED. YOU MAY FIND IT TO WORK JUST FINE, OR NOT

To get this api running locally:

1. Install virtualbox and vagrant
2. Run vagrant up on root
3. You should see a lot of stuff happening, this takes some time.

You can access the the virtual machine by running vagrant ssh. If it asks for a password, its 'vagrant'.
In the virtual machine you can migrate can seed the database with these commands:

1. Navigate to /var/www/ticketless-api
2. Run sudo bash initialize_local_db.sh

## Troubleshoot

If running vagrant tells you there is something wrong with setting up the virtualbox, it's possible that
you have disabled some kind of virtualization setting. One typical case involves riot games vanguard:
at least on windows 10, in order to use vanguard virtualization has to be set off, which goes against
vagrants needs.

If something, somewhere goes terribly wrong, just contact Henri :D better to do that than blindly edit vagrantfile.

## License

The Lumen framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

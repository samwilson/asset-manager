Assets
======

Assets are at the core of AssetManager.
They are the fundamental unit that all other parts of the system refer back to.

An Asset is any physical piece of infrastructure, equipment, or machinery.
It has a unique alphanumeric identifier, and a fixed location.
Asset can be tagged with any number of tags.

New Assets
----------

After first installing AssetManager, the database is basically empty.
The first action you will take (aside from changing the Admin password from the default)
is to create some asset records.
This can be done one at a time, or en masse via the import of a CSV file.

To create a single Asset record, navigate to the :menuselection:`Asssets --> Create` menu item and fill in the required information.
Details about the various fields are given below.

To import Assets, first create a CSV file with column headers as specified below.
The order that these headers appear in is not important,
neither is leading or trailing whitespace within any cells of the CSV.

Required CSV headers:

+------------+------+---------+-------+--------+----------------+----------------------+----------+-----------+----------+
| Identifier | Tags | Country | State | Suburb | Street address | Location description | Latitude | Longitude | Comments |
+------------+------+---------+-------+--------+----------------+----------------------+----------+-----------+----------+

The Asset **Identifier** is the most important: it can be whatever is appropriate
but it must be unique across all Assets that are to be included in the system.
The Identifier can be up to 100 characters long.

The **location** attributes are mostly self-explanatory,
apart from the *Location description* which is an optional field
for describing the location of an Asset within the relevant *Street address*.

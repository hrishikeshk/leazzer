package leazzerScraperMain;

import javax.swing.*;
import javax.swing.border.Border;

import java.awt.*;
import java.awt.event.ActionListener;
import java.awt.event.ActionEvent;

public class SwingMain implements Runnable  {
    private final JButton submit = new JButton();
    private final JButton quit = new JButton();

    private final JTextField adminUser = new JTextField();
    private final JTextField adminPass = new JPasswordField();
    private final JCheckBox enableLogFile = new JCheckBox();

    private final JTextField cityPin = new JTextField();
    private JFrame f;
    public void run() {
        f = new JFrame("Leazzer : Fetch and Scrape !");
        f.setBounds(200, 200, 1000, 1000);
        f.setPreferredSize(new Dimension(700, 200));
        f.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);

        f.setLayout(new GridLayout(5, 2));

        f.add(new JLabel("Admin Username: "));
        f.add(adminUser);

        f.add(new JLabel("Admin Password: "));
        f.add(adminPass);

        f.add(new JLabel("Enable File logging: "));
        f.add(enableLogFile);

        f.add(new JLabel("City / Zip (Mandatory input) : "));
        f.add(cityPin);

        f.add(new JLabel("Fetch and scrape ... "));

        JPanel fi = new JPanel();
        f.add(fi);
        fi.setLayout((new GridLayout(1, 2)));
        makeButton(submit);
        fi.add(submit);
        makeQuitButton(quit);
        quit.setEnabled(false);
        fi.add(quit);

        f.pack();

        f.setVisible(true);
    }

    private JButton makeButton(JButton sb) {
        sb.setText("Start");

        sb.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent e) {
                String[] cityPinArr = new String[3];
                cityPinArr[0] = adminUser.getText();
                cityPinArr[1] = adminPass.getText();
                cityPinArr[2] = cityPin.getText();
                if(cityPinArr[2].length() > 0) {
                    quit.setEnabled(true);
                    submit.setEnabled(false);
                    f.setVisible(true);
                    MainImplementation mi = new MainImplementation();
                    mi.cmdArgs = cityPinArr;
                    mi.enableLogFile = enableLogFile.isSelected();
                    new Thread(mi).start();

                    //MainImplementation.mainImpl(cityPinArr, enableLogFile.isSelected());
                }
            }
        });
        return sb;
    }

    private JButton makeQuitButton(JButton sb) {
        sb.setText("Force Quit !");

        sb.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent e) {
                System.exit(0);
            }
        });
        return sb;
    }

    public static void swingMainEntry() {
        SwingMain se = new SwingMain();

        SwingUtilities.invokeLater(se);
    }
}
